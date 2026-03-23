<?php

namespace App\Http\Controllers\Api\V1\Parcel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Parcel\StoreParcelSyncRequest;
use App\Http\Requests\Api\Parcel\UpdateParcelStatusRequest;
use App\Models\Parcel;
use App\Models\SyncLog;
use App\Services\ParcelStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ParcelSyncController extends Controller
{
    public function __construct(
        private readonly ParcelStatusService $parcelStatusService,
    ) {}

    public function store(StoreParcelSyncRequest $request): JsonResponse
    {
        $payload = $request->validatedWithDefaults();
        $user = $request->user();

        $existingParcel = Parcel::query()
            ->where('tracking_id', $payload['tracking_id'])
            ->first();

        if ($existingParcel) {
            $response = [
                'success' => false,
                'message' => 'Duplicate tracking ID.',
                'error_code' => 'DUPLICATE_TRACKING_ID',
                'data' => [
                    'parcel_id' => $existingParcel->id,
                    'tracking_id' => $existingParcel->tracking_id,
                    'status' => $existingParcel->status,
                ],
            ];

            $this->writeSyncLog(
                action: 'create',
                syncStatus: 'failed',
                requestPayload: $payload,
                responsePayload: $response,
                parcelId: $existingParcel->id,
                trackingId: $payload['tracking_id'],
                userId: $user?->id,
                accountCode: $payload['account_code'] ?? null,
                errorMessage: 'Duplicate tracking ID.'
            );

            return response()->json($response, 409);
        }

        $parcel = DB::transaction(function () use ($payload, $user) {
            $parcel = Parcel::query()->create([
                'tracking_id' => $payload['tracking_id'],
                'from_town' => $payload['from_town'],
                'to_town' => $payload['to_town'],
                'city_code' => $payload['city_code'],
                'account_code' => $payload['account_code'],
                'sender_name' => $payload['sender_name'],
                'sender_phone' => $payload['sender_phone'],
                'receiver_name' => $payload['receiver_name'],
                'receiver_phone' => $payload['receiver_phone'],
                'parcel_type' => $payload['parcel_type'],
                'number_of_parcels' => $payload['number_of_parcels'],
                'total_charges' => $payload['total_charges'],
                'payment_status' => $payload['payment_status'],
                'cash_advance' => $payload['cash_advance'],
                'parcel_image_path' => $payload['parcel_image_path'] ?? null,
                'remark' => $payload['remark'] ?? null,
                'status' => $payload['status'],
                'sync_status' => 'synced',
                'synced_at' => now(),
                'arrived_at' => $payload['arrived_at'] ?? null,
                'claimed_at' => $payload['claimed_at'] ?? null,
            ]);

            $this->parcelStatusService->initialize(
                parcel: $parcel,
                status: $payload['status'],
                changedBy: $user,
                note: 'Created from mobile sync.',
            );

            return $parcel->load(['fromTown:id,town_name', 'toTown:id,town_name']);
        });

        $response = [
            'success' => true,
            'message' => 'Parcel synced successfully.',
            'data' => [
                'id' => $parcel->id,
                'tracking_id' => $parcel->tracking_id,
                'status' => $parcel->status,
                'sync_status' => $parcel->sync_status,
                'synced_at' => $parcel->synced_at?->toISOString(),
                'from_town' => [
                    'id' => $parcel->fromTown?->id,
                    'town_name' => $parcel->fromTown?->town_name,
                ],
                'to_town' => [
                    'id' => $parcel->toTown?->id,
                    'town_name' => $parcel->toTown?->town_name,
                ],
            ],
        ];

        $this->writeSyncLog(
            action: 'create',
            syncStatus: 'success',
            requestPayload: $payload,
            responsePayload: $response,
            parcelId: $parcel->id,
            trackingId: $parcel->tracking_id,
            userId: $user?->id,
            accountCode: $parcel->account_code,
        );

        return response()->json($response, 201);
    }

    public function show(string $trackingId): JsonResponse
    {
        $parcel = Parcel::query()
            ->with(['fromTown:id,town_name', 'toTown:id,town_name'])
            ->where('tracking_id', $trackingId)
            ->first();

        if (! $parcel) {
            return response()->json([
                'success' => false,
                'message' => 'Parcel not found.',
                'error_code' => 'PARCEL_NOT_FOUND',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Parcel found.',
            'data' => [
                'id' => $parcel->id,
                'tracking_id' => $parcel->tracking_id,
                'status' => $parcel->status,
                'payment_status' => $parcel->payment_status,
                'sync_status' => $parcel->sync_status,
                'account_code' => $parcel->account_code,
                'sender_name' => $parcel->sender_name,
                'sender_phone' => $parcel->sender_phone,
                'receiver_name' => $parcel->receiver_name,
                'receiver_phone' => $parcel->receiver_phone,
                'parcel_type' => $parcel->parcel_type,
                'number_of_parcels' => $parcel->number_of_parcels,
                'total_charges' => $parcel->total_charges,
                'cash_advance' => $parcel->cash_advance,
                'remark' => $parcel->remark,
                'arrived_at' => $parcel->arrived_at?->toISOString(),
                'claimed_at' => $parcel->claimed_at?->toISOString(),
                'synced_at' => $parcel->synced_at?->toISOString(),
                'from_town' => [
                    'id' => $parcel->fromTown?->id,
                    'town_name' => $parcel->fromTown?->town_name,
                ],
                'to_town' => [
                    'id' => $parcel->toTown?->id,
                    'town_name' => $parcel->toTown?->town_name,
                ],
            ],
        ]);
    }

    public function updateStatus(UpdateParcelStatusRequest $request, string $trackingId): JsonResponse
    {
        $parcel = Parcel::query()->where('tracking_id', $trackingId)->first();

        if (! $parcel) {
            return response()->json([
                'success' => false,
                'message' => 'Parcel not found.',
                'error_code' => 'PARCEL_NOT_FOUND',
            ], 404);
        }

        try {
            $parcel = $this->parcelStatusService->transition(
                parcel: $parcel,
                newStatus: $request->string('status')->toString(),
                changedBy: $request->user(),
                note: $request->input('note'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'error_code' => 'INVALID_STATUS_TRANSITION',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Parcel status updated successfully.',
            'data' => [
                'id' => $parcel->id,
                'tracking_id' => $parcel->tracking_id,
                'status' => $parcel->status,
                'arrived_at' => $parcel->arrived_at?->toISOString(),
                'claimed_at' => $parcel->claimed_at?->toISOString(),
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $requestPayload
     * @param  array<string, mixed>  $responsePayload
     */
    private function writeSyncLog(
        string $action,
        string $syncStatus,
        array $requestPayload,
        array $responsePayload,
        ?int $parcelId = null,
        ?string $trackingId = null,
        ?int $userId = null,
        ?string $accountCode = null,
        ?string $errorMessage = null,
    ): void {
        SyncLog::query()->create([
            'parcel_id' => $parcelId,
            'tracking_id' => $trackingId,
            'user_id' => $userId,
            'account_code' => $accountCode,
            'action' => $action,
            'sync_status' => $syncStatus,
            'error_message' => $errorMessage,
            'request_payload' => $requestPayload,
            'response_payload' => $responsePayload,
        ]);
    }
}

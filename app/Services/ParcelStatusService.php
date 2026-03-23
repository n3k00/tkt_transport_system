<?php

namespace App\Services;

use App\Models\Parcel;
use App\Models\ParcelStatusLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ParcelStatusService
{
    /**
     * @var array<string, string|null>
     */
    private const ALLOWED_PREVIOUS_STATUS = [
        'received' => null,
        'dispatched' => 'received',
        'arrived' => 'dispatched',
        'claimed' => 'arrived',
    ];

    public function initialize(Parcel $parcel, string $status, ?User $changedBy = null, ?string $note = null): Parcel
    {
        if ($status !== 'received') {
            throw new InvalidArgumentException('Initial parcel status must be [received].');
        }

        return DB::transaction(function () use ($parcel, $status, $changedBy, $note) {
            $this->applyStatusSideEffects($parcel, $status);
            $parcel->status = $status;
            $parcel->save();

            ParcelStatusLog::query()->create([
                'parcel_id' => $parcel->id,
                'previous_status' => null,
                'new_status' => $status,
                'changed_by' => $changedBy?->id,
                'note' => $note,
                'created_at' => now(),
            ]);

            return $parcel;
        });
    }

    public function transition(Parcel $parcel, string $newStatus, ?User $changedBy = null, ?string $note = null): Parcel
    {
        $expectedPreviousStatus = self::ALLOWED_PREVIOUS_STATUS[$newStatus] ?? '__invalid__';

        if ($expectedPreviousStatus === '__invalid__') {
            throw new InvalidArgumentException('Invalid parcel status.');
        }

        if ($parcel->status !== $expectedPreviousStatus) {
            throw new InvalidArgumentException("Invalid status transition from [{$parcel->status}] to [{$newStatus}].");
        }

        return DB::transaction(function () use ($parcel, $newStatus, $changedBy, $note) {
            $previousStatus = $parcel->status;

            $this->applyStatusSideEffects($parcel, $newStatus);
            $parcel->status = $newStatus;
            $parcel->save();

            ParcelStatusLog::query()->create([
                'parcel_id' => $parcel->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'changed_by' => $changedBy?->id,
                'note' => $note,
                'created_at' => now(),
            ]);

            return $parcel;
        });
    }

    private function applyStatusSideEffects(Parcel $parcel, string $status): void
    {
        if ($status === 'arrived' && ! $parcel->arrived_at) {
            $parcel->arrived_at = now();
        }

        if ($status === 'claimed' && ! $parcel->claimed_at) {
            $parcel->claimed_at = now();
        }
    }
}

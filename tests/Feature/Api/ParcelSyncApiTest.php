<?php

namespace Tests\Feature\Api;

use App\Models\Driver;
use App\Models\Item;
use App\Models\Merchant;
use App\Models\Parcel;
use App\Models\Town;
use App\Models\User;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\StockEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ParcelSyncApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_parcel_sync_success_creates_parcel_and_returns_success_response(): void
    {
        $user = User::factory()->create([
            'phone' => '09922222222',
            'role' => 'staff',
            'account_code' => 'ACC00001',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();
        $payload = $this->validPayload($sourceTown, $destinationTown, 'TRK-1001');

        $response = $this->postJson('/api/v1/parcels/sync', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Parcel synced successfully.')
            ->assertJsonPath('data.tracking_id', 'TRK-1001')
            ->assertJsonPath('data.status', 'received')
            ->assertJsonPath('data.sync_status', 'synced');

        $this->assertDatabaseHas('parcels', [
            'tracking_id' => 'TRK-1001',
            'account_code' => 'ACC00001',
            'status' => 'received',
            'sync_status' => 'synced',
        ]);
    }

    public function test_parcel_sync_fail_returns_validation_errors(): void
    {
        $user = User::factory()->create([
            'phone' => '09933333333',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();
        $payload = $this->validPayload($sourceTown, $destinationTown, 'TRK-1002');
        $payload['sender_phone'] = '08123456789';

        $response = $this->postJson('/api/v1/parcels/sync', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonValidationErrors(['sender_phone']);

        $this->assertDatabaseMissing('parcels', [
            'tracking_id' => 'TRK-1002',
        ]);
    }

    public function test_parcel_sync_fails_when_account_code_does_not_match_authenticated_user(): void
    {
        $user = User::factory()->create([
            'phone' => '09933444444',
            'account_code' => 'ACC99999',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();
        $payload = $this->validPayload($sourceTown, $destinationTown, 'TRK-1002A');
        $payload['account_code'] = 'ACC00001';

        $this->postJson('/api/v1/parcels/sync', $payload)
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonValidationErrors(['account_code']);

        $this->assertDatabaseMissing('parcels', [
            'tracking_id' => 'TRK-1002A',
        ]);
    }

    public function test_parcel_sync_fails_when_city_code_does_not_match_source_town(): void
    {
        $user = User::factory()->create([
            'phone' => '09933555555',
            'account_code' => 'ACC00001',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();
        $payload = $this->validPayload($sourceTown, $destinationTown, 'TRK-1002B');
        $payload['city_code'] = 'WRONG';

        $this->postJson('/api/v1/parcels/sync', $payload)
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonValidationErrors(['city_code']);

        $this->assertDatabaseMissing('parcels', [
            'tracking_id' => 'TRK-1002B',
        ]);
    }

    public function test_parcel_sync_requires_from_town_to_be_source_type(): void
    {
        $user = User::factory()->create([
            'phone' => '09933666666',
            'account_code' => 'ACC00001',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();
        $payload = $this->validPayload($sourceTown, $destinationTown, 'TRK-1002C');
        $payload['from_town'] = $destinationTown->id;

        $this->postJson('/api/v1/parcels/sync', $payload)
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonValidationErrors(['from_town']);
    }

    public function test_parcel_sync_requires_to_town_to_be_destination_type(): void
    {
        $user = User::factory()->create([
            'phone' => '09933777777',
            'account_code' => 'ACC00001',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();
        $payload = $this->validPayload($sourceTown, $destinationTown, 'TRK-1002D');
        $payload['to_town'] = $sourceTown->id;

        $this->postJson('/api/v1/parcels/sync', $payload)
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonValidationErrors(['to_town']);
    }

    public function test_duplicate_tracking_id_returns_conflict_and_failed_sync_log(): void
    {
        $user = User::factory()->create([
            'phone' => '09944444444',
            'account_code' => 'ACC00001',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();

        $existingParcel = Parcel::query()->create([
            'tracking_id' => 'TRK-1003',
            'from_town' => $sourceTown->id,
            'to_town' => $destinationTown->id,
            'city_code' => 'TGI',
            'account_code' => 'ACC00002',
            'sender_name' => 'Existing Sender',
            'sender_phone' => '09955555555',
            'receiver_name' => 'Existing Receiver',
            'receiver_phone' => '09966666666',
            'parcel_type' => 'Box',
            'number_of_parcels' => 1,
            'total_charges' => 2000,
            'payment_status' => 'paid',
            'cash_advance' => 0,
            'status' => 'received',
            'sync_status' => 'synced',
            'synced_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/parcels/sync', $this->validPayload($sourceTown, $destinationTown, 'TRK-1003'));

        $response
            ->assertStatus(409)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Duplicate tracking ID.')
            ->assertJsonPath('error_code', 'DUPLICATE_TRACKING_ID')
            ->assertJsonPath('data.parcel_id', $existingParcel->id);

        $this->assertDatabaseHas('sync_logs', [
            'parcel_id' => $existingParcel->id,
            'tracking_id' => 'TRK-1003',
            'user_id' => $user->id,
            'account_code' => 'ACC00001',
            'action' => 'create',
            'sync_status' => 'failed',
            'error_message' => 'Duplicate tracking ID.',
        ]);
    }

    public function test_parcel_status_change_creates_status_log_with_changed_by_and_note(): void
    {
        $user = User::factory()->create([
            'phone' => '09977777777',
            'account_code' => 'ACC00001',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();
        $this->postJson('/api/v1/parcels/sync', $this->validPayload($sourceTown, $destinationTown, 'TRK-1004'))
            ->assertCreated();

        $response = $this->patchJson('/api/v1/parcels/tracking/TRK-1004/status', [
            'status' => 'dispatched',
            'note' => 'Sent to route vehicle.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'dispatched');

        $parcelId = Parcel::query()->where('tracking_id', 'TRK-1004')->value('id');

        $this->assertDatabaseHas('parcel_status_logs', [
            'parcel_id' => $parcelId,
            'previous_status' => 'received',
            'new_status' => 'dispatched',
            'changed_by' => $user->id,
            'note' => 'Sent to route vehicle.',
        ]);
    }

    public function test_successful_sync_creates_sync_log_with_request_and_response_trace(): void
    {
        $user = User::factory()->create([
            'phone' => '09988888888',
            'account_code' => 'ACC00001',
        ]);

        Sanctum::actingAs($user);

        [$sourceTown, $destinationTown] = $this->createTowns();
        $payload = $this->validPayload($sourceTown, $destinationTown, 'TRK-1005');

        $response = $this->postJson('/api/v1/parcels/sync', $payload)->assertCreated();

        $parcelId = Parcel::query()->where('tracking_id', 'TRK-1005')->value('id');

        $this->assertDatabaseHas('sync_logs', [
            'parcel_id' => $parcelId,
            'tracking_id' => 'TRK-1005',
            'user_id' => $user->id,
            'account_code' => 'ACC00001',
            'action' => 'create',
            'sync_status' => 'success',
            'error_message' => null,
        ]);

        $this->assertDatabaseHas('parcel_status_logs', [
            'parcel_id' => $parcelId,
            'previous_status' => null,
            'new_status' => 'received',
            'changed_by' => $user->id,
            'note' => 'Created from mobile sync.',
        ]);

        $syncLog = \App\Models\SyncLog::query()->where('tracking_id', 'TRK-1005')->firstOrFail();

        $this->assertSame('TRK-1005', $syncLog->request_payload['tracking_id']);
        $this->assertTrue($syncLog->response_payload['success']);
        $this->assertSame('TRK-1005', $response->json('data.tracking_id'));
    }

    public function test_shipment_item_cannot_exceed_available_stock_balance(): void
    {
        $merchant = Merchant::query()->create([
            'name' => 'Merchant A',
            'is_active' => true,
        ]);

        $item = Item::query()->create([
            'item_name' => 'Rice Bag',
            'unit' => 'bag',
        ]);

        $driver = Driver::query()->create([
            'name' => 'Driver A',
            'is_active' => true,
        ]);

        StockEntry::query()->create([
            'merchant_id' => $merchant->id,
            'item_id' => $item->id,
            'quantity' => 5,
            'unit_price' => 1000,
            'line_total' => 5000,
            'received_date' => now()->toDateString(),
        ]);

        $shipment = Shipment::query()->create([
            'shipment_no' => 'SHP-001',
            'shipment_date' => now()->toDateString(),
            'driver_id' => $driver->id,
            'car_number' => '7K-1234',
            'total_amount' => 0,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        ShipmentItem::query()->create([
            'shipment_id' => $shipment->id,
            'merchant_id' => $merchant->id,
            'item_id' => $item->id,
            'quantity' => 6,
            'unit_price' => 1000,
            'line_total' => 6000,
        ]);
    }

    public function test_shipment_item_line_total_is_calculated_automatically(): void
    {
        $merchant = Merchant::query()->create([
            'name' => 'Merchant B',
            'is_active' => true,
        ]);

        $item = Item::query()->create([
            'item_name' => 'Oil Can',
            'unit' => 'can',
        ]);

        $driver = Driver::query()->create([
            'name' => 'Driver B',
            'default_car_number' => '9M-1111',
            'is_active' => true,
        ]);

        StockEntry::query()->create([
            'merchant_id' => $merchant->id,
            'item_id' => $item->id,
            'quantity' => 20,
            'unit_price' => 500,
            'line_total' => 10000,
            'received_date' => now()->toDateString(),
        ]);

        $shipment = Shipment::query()->create([
            'shipment_no' => 'SHP-002',
            'shipment_date' => now()->toDateString(),
            'driver_id' => $driver->id,
            'car_number' => $driver->default_car_number,
            'total_amount' => 0,
        ]);

        $shipmentItem = ShipmentItem::query()->create([
            'shipment_id' => $shipment->id,
            'merchant_id' => $merchant->id,
            'item_id' => $item->id,
            'quantity' => 3,
            'unit_price' => 1500,
            'line_total' => 0,
        ]);

        $this->assertSame('4500.00', $shipmentItem->fresh()->line_total);
    }

    public function test_shipment_total_amount_is_recalculated_when_items_are_created_updated_and_deleted(): void
    {
        $merchant = Merchant::query()->create([
            'name' => 'Merchant C',
            'is_active' => true,
        ]);

        $item = Item::query()->create([
            'item_name' => 'Soap',
            'unit' => 'box',
        ]);

        $driver = Driver::query()->create([
            'name' => 'Driver C',
            'default_car_number' => '8L-2222',
            'is_active' => true,
        ]);

        StockEntry::query()->create([
            'merchant_id' => $merchant->id,
            'item_id' => $item->id,
            'quantity' => 50,
            'unit_price' => 200,
            'line_total' => 10000,
            'received_date' => now()->toDateString(),
        ]);

        $shipment = Shipment::query()->create([
            'shipment_no' => 'SHP-003',
            'shipment_date' => now()->toDateString(),
            'driver_id' => $driver->id,
            'car_number' => $driver->default_car_number,
            'total_amount' => 0,
        ]);

        $firstItem = ShipmentItem::query()->create([
            'shipment_id' => $shipment->id,
            'merchant_id' => $merchant->id,
            'item_id' => $item->id,
            'quantity' => 5,
            'unit_price' => 200,
            'line_total' => 0,
        ]);

        $this->assertSame('1000.00', $shipment->fresh()->total_amount);

        $secondItem = ShipmentItem::query()->create([
            'shipment_id' => $shipment->id,
            'merchant_id' => $merchant->id,
            'item_id' => $item->id,
            'quantity' => 4,
            'unit_price' => 300,
            'line_total' => 0,
        ]);

        $this->assertSame('2200.00', $shipment->fresh()->total_amount);

        $secondItem->update([
            'quantity' => 6,
            'unit_price' => 300,
        ]);

        $this->assertSame('2800.00', $shipment->fresh()->total_amount);

        $firstItem->delete();

        $this->assertSame('1800.00', $shipment->fresh()->total_amount);
    }

    public function test_driver_default_car_number_can_flow_to_shipment_car_number(): void
    {
        $driver = Driver::query()->create([
            'name' => 'Driver D',
            'phone' => '09912312312',
            'default_car_number' => '1A-9999',
            'is_active' => true,
        ]);

        $shipment = Shipment::query()->create([
            'shipment_no' => 'SHP-004',
            'shipment_date' => now()->toDateString(),
            'driver_id' => $driver->id,
            'car_number' => $driver->default_car_number,
            'total_amount' => 0,
        ]);

        $shipment->refresh();

        $this->assertSame($driver->id, $shipment->driver_id);
        $this->assertSame('1A-9999', $shipment->car_number);
        $this->assertSame('1A-9999', $shipment->driver->default_car_number);
    }

    /**
     * @return array{0: Town, 1: Town}
     */
    private function createTowns(): array
    {
        $sourceTown = Town::query()->create([
            'town_name' => 'တောင်ကြီး',
            'type' => 'source',
            'city_code' => 'TGI',
            'sort_order' => 1,
        ]);

        $destinationTown = Town::query()->create([
            'town_name' => 'ရန်ကုန်',
            'type' => 'destination',
            'city_code' => null,
            'sort_order' => 1,
        ]);

        return [$sourceTown, $destinationTown];
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(Town $sourceTown, Town $destinationTown, string $trackingId): array
    {
        return [
            'tracking_id' => $trackingId,
            'from_town' => $sourceTown->id,
            'to_town' => $destinationTown->id,
            'city_code' => 'TGI',
            'account_code' => 'ACC00001',
            'sender_name' => 'Sender Name',
            'sender_phone' => '09912345678',
            'receiver_name' => 'Receiver Name',
            'receiver_phone' => '09987654321',
            'parcel_type' => 'Box',
            'number_of_parcels' => 2,
            'total_charges' => 5000,
            'payment_status' => 'paid',
            'cash_advance' => 0,
            'remark' => 'Handle with care',
            'status' => 'received',
        ];
    }
}

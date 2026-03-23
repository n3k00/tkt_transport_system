<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->foreignId('from_town')->constrained('towns')->restrictOnDelete();
            $table->foreignId('to_town')->constrained('towns')->restrictOnDelete();
            $table->string('city_code', 20);
            $table->string('account_code');
            $table->string('sender_name');
            $table->string('sender_phone', 11);
            $table->string('receiver_name');
            $table->string('receiver_phone', 11);
            $table->string('parcel_type');
            $table->unsignedInteger('number_of_parcels');
            $table->decimal('total_charges', 12, 2);
            $table->string('payment_status', 20);
            $table->decimal('cash_advance', 12, 2)->default(0);
            $table->string('parcel_image_path')->nullable();
            $table->text('remark')->nullable();
            $table->string('status', 20)->default('received');
            $table->string('sync_status', 20)->default('pending');
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('from_town');
            $table->index('to_town');
            $table->index('city_code');
            $table->index('account_code');
            $table->index('sender_phone');
            $table->index('receiver_phone');
            $table->index('status');
            $table->index('payment_status');
            $table->index('sync_status');
            $table->index('created_at');
        });

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(<<<'SQL'
            ALTER TABLE parcels
            ADD CONSTRAINT parcels_status_check
            CHECK (status IN ('received', 'dispatched', 'arrived', 'claimed'))
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE parcels
            ADD CONSTRAINT parcels_payment_status_check
            CHECK (payment_status IN ('paid', 'unpaid'))
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE parcels
            ADD CONSTRAINT parcels_sync_status_check
            CHECK (sync_status IN ('pending', 'synced', 'failed'))
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE parcels
            ADD CONSTRAINT parcels_number_of_parcels_check
            CHECK (number_of_parcels > 0)
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE parcels
            ADD CONSTRAINT parcels_total_charges_check
            CHECK (total_charges >= 0)
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE parcels
            ADD CONSTRAINT parcels_cash_advance_check
            CHECK (cash_advance >= 0)
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};

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
        Schema::create('parcel_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->constrained('parcels')->cascadeOnDelete();
            $table->string('previous_status', 20)->nullable();
            $table->string('new_status', 20);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['parcel_id', 'created_at']);
            $table->index('new_status');
            $table->index('changed_by');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE parcel_status_logs
            ADD CONSTRAINT parcel_status_logs_previous_status_check
            CHECK (
                previous_status IS NULL
                OR previous_status IN ('received', 'dispatched', 'arrived', 'claimed')
            )
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE parcel_status_logs
            ADD CONSTRAINT parcel_status_logs_new_status_check
            CHECK (new_status IN ('received', 'dispatched', 'arrived', 'claimed'))
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_status_logs');
    }
};

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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->nullable()->constrained('parcels')->nullOnDelete();
            $table->string('tracking_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('account_code')->nullable();
            $table->string('action', 20);
            $table->string('sync_status', 20);
            $table->text('error_message')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();

            $table->index('tracking_id');
            $table->index('user_id');
            $table->index('account_code');
            $table->index('parcel_id');
            $table->index('created_at');
            $table->index(['sync_status', 'created_at']);
            $table->index(['action', 'created_at']);
        });

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(<<<'SQL'
            ALTER TABLE sync_logs
            ADD CONSTRAINT sync_logs_action_check
            CHECK (action IN ('create', 'update', 'retry'))
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE sync_logs
            ADD CONSTRAINT sync_logs_status_check
            CHECK (sync_status IN ('success', 'failed'))
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};

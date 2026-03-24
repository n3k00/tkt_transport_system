<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_no')->unique();
            $table->date('shipment_date');
            $table->foreignId('driver_id')->constrained()->restrictOnDelete();
            $table->string('car_number', 50);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->index('shipment_date');
            $table->index('driver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

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
        Schema::create('towns', function (Blueprint $table) {
            $table->id();
            $table->string('town_name');
            $table->string('type', 20);
            $table->string('city_code', 20)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['town_name', 'type']);
            $table->index(['type', 'sort_order']);
        });

        DB::statement(<<<'SQL'
            ALTER TABLE towns
            ADD CONSTRAINT towns_type_check
            CHECK (type IN ('source', 'destination'))
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE towns
            ADD CONSTRAINT towns_city_code_by_type_check
            CHECK (
                (type = 'source' AND city_code IS NOT NULL)
                OR (type = 'destination')
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('towns');
    }
};

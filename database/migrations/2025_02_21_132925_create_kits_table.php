<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('kits', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->char('team_id', 26);  // Changed to char(26) for ULID
            $table->foreign('team_id')
                  ->references('id')
                  ->on('teams')
                  ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kit_asset', function (Blueprint $table) {
            $table->id();
            $table->char('kit_id', 26);  // Changed to char(26) for ULID
            $table->char('asset_id', 26);  // Changed to char(26) for ULID
            $table->timestamps();

            $table->foreign('kit_id')
                  ->references('id')
                  ->on('kits')
                  ->onDelete('cascade');
            $table->foreign('asset_id')
                  ->references('id')
                  ->on('assets')
                  ->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop pivot tables first
        Schema::dropIfExists('kit_asset');
        Schema::dropIfExists('kit_custom_field_values');

        // Then drop the main table
        Schema::dropIfExists('kits');

        Schema::enableForeignKeyConstraints();
    }
};

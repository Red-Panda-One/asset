<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, drop the existing table
        Schema::dropIfExists('asset_additional_file');

        // Recreate the table with a new structure
        Schema::create('asset_additional_file', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('asset_id');
            $table->ulid('additional_file_id');
            $table->timestamps();

            $table->foreign('asset_id')
                  ->references('id')
                  ->on('assets')
                  ->onDelete('cascade');
            $table->foreign('additional_file_id')
                  ->references('id')
                  ->on('additional_files')
                  ->onDelete('cascade');

            // Add a unique constraint to prevent duplicate associations
            $table->unique(['asset_id', 'additional_file_id']);
        });
    }

    public function down(): void
    {
        // Drop the new table
        Schema::dropIfExists('asset_additional_file');

        // Recreate the original table structure
        Schema::create('asset_additional_file', function (Blueprint $table) {
            $table->ulid('asset_id');
            $table->ulid('additional_file_id');
            $table->timestamps();

            $table->foreign('asset_id')
                  ->references('id')
                  ->on('assets')
                  ->onDelete('cascade');
            $table->foreign('additional_file_id')
                  ->references('id')
                  ->on('additional_files')
                  ->onDelete('cascade');

            $table->primary(['asset_id', 'additional_file_id']);
        });
    }
};

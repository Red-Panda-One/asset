<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('custom_id')->nullable()->after('id');
        });

        Schema::table('kits', function (Blueprint $table) {
            $table->string('custom_id')->nullable()->after('id');
        });

        Schema::create('additional_files', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('file_path');
            $table->string('name');
            $table->string('mime_type');
            $table->integer('size');
            $table->char('team_id', 26);  // Add team_id column
            $table->integer('linked_count')->default(0);  // Add linked_count column
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('team_id')
                  ->references('id')
                  ->on('teams')
                  ->onDelete('cascade');
        });

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

        Schema::create('kit_additional_file', function (Blueprint $table) {
            $table->ulid('kit_id');
            $table->ulid('additional_file_id');
            $table->timestamps();

            $table->foreign('kit_id')
                  ->references('id')
                  ->on('kits')
                  ->onDelete('cascade');
            $table->foreign('additional_file_id')
                  ->references('id')
                  ->on('additional_files')
                  ->onDelete('cascade');

            $table->primary(['kit_id', 'additional_file_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_additional_file');
        Schema::dropIfExists('asset_additional_file');
        Schema::dropIfExists('additional_files');

        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn('custom_id');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('custom_id');
        });
    }
};

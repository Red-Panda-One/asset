<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->boolean('required')->default(false);
            $table->boolean('active')->default(true);
            $table->boolean('category_specific')->default(false);
            $table->timestamps();
        });

        Schema::create('category_custom_field', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('category_id');
            $table->ulid('custom_field_id');
            $table->timestamps();

            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
            $table->foreign('custom_field_id')
                  ->references('id')
                  ->on('custom_fields')
                  ->onDelete('cascade');

            $table->unique(['category_id', 'custom_field_id']);
        });

        Schema::create('custom_field_options', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('custom_field_id');
            $table->string('value');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('custom_field_id')
                  ->references('id')
                  ->on('custom_fields')
                  ->onDelete('cascade');
        });

        Schema::create('asset_custom_field_values', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('asset_id');
            $table->ulid('custom_field_id');
            $table->text('value');
            $table->timestamps();

            $table->foreign('asset_id')
                  ->references('id')
                  ->on('assets')
                  ->onDelete('cascade');
            $table->foreign('custom_field_id')
                  ->references('id')
                  ->on('custom_fields')
                  ->onDelete('cascade');

            $table->unique(['asset_id', 'custom_field_id']);
        });

        Schema::create('kit_custom_field_values', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('kit_id');
            $table->ulid('custom_field_id');
            $table->text('value');
            $table->timestamps();

            $table->foreign('kit_id')
                  ->references('id')
                  ->on('kits')
                  ->onDelete('cascade');
            $table->foreign('custom_field_id')
                  ->references('id')
                  ->on('custom_fields')
                  ->onDelete('cascade');

            $table->unique(['kit_id', 'custom_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_custom_field_values');
        Schema::dropIfExists('asset_custom_field_values');
        Schema::dropIfExists('custom_field_options');
        Schema::dropIfExists('category_custom_field');
        Schema::dropIfExists('custom_fields');
    }
};

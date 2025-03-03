<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop the existing table if it exists
        Schema::dropIfExists('kit_additional_file');

        // Create the table with the correct structure
        Schema::create('kit_additional_file', function (Blueprint $table) {
            $table->foreignUlid('kit_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('additional_file_id')->constrained('additional_files')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['kit_id', 'additional_file_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kit_additional_file');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAssetsAndKitsTables extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('status')->default('Available');
        });

        Schema::table('kits', function (Blueprint $table) {
            $table->string('status')->default('Available');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}

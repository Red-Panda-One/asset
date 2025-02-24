<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->integer('asset_count')->default(0);
        });
    }

    public function down()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn('asset_count');
        });
    }
};

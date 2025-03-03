<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('game_packages', function (Blueprint $table) {
            $table->boolean('highlight')->default(false)->after('sort_order');
        });
    }

    public function down()
    {
        Schema::table('game_packages', function (Blueprint $table) {
            $table->dropColumn('highlight');
        });
    }
};

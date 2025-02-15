<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coin_transactions', function (Blueprint $table) {
            $table->integer('coin_earned')->default(0)->after('coins_used'); // Add column for coin_earned
        });
    }
    
    public function down()
    {
        Schema::table('coin_transactions', function (Blueprint $table) {
            $table->dropColumn('coin_earned');
        });
    }
    
};

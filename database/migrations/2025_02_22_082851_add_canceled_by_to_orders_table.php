<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('canceled_by')->nullable()->after('status');
            $table->foreign('canceled_by')->references('id')->on('admins')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['canceled_by']);
            $table->dropColumn('canceled_by');
        });
    }
    
};

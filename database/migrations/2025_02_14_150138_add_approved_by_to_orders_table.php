<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by')->nullable();  // Store admin's ID
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');  // Assuming your admin users are in the 'users' table
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('approved_by');
        });
    }
    
};

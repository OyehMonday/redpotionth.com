<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add the 'in_process_by' column which references the 'admins' table
            $table->unsignedBigInteger('in_process_by')->nullable()->after('status'); // Or adjust the column position as necessary
            $table->foreign('in_process_by')->references('id')->on('admins')->onDelete('set null'); // Foreign key constraint to the admins table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['in_process_by']);
            $table->dropColumn('in_process_by');
        });
    }
    
};

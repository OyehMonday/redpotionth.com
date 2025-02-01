<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('game_packages', function (Blueprint $table) {
            $table->text('detail')->nullable()->after('price'); // Add package detail field
            $table->integer('sort_order')->default(0)->after('detail'); // Add sort order field for sorting
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_packages', function (Blueprint $table) {
            //
        });
    }
};

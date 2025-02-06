<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('uid_detail')->nullable()->after('cover_image'); 
            $table->string('uid_image')->nullable()->after('uid_detail'); 
        });
    }

    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['uid_detail', 'uid_image']);
        });
    }
};

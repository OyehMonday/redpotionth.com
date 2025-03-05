<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('refslip')->nullable()->after('payment_slip'); // Reference number from slip (OCR)
            $table->string('refqr')->nullable()->after('refslip'); // Reference number from QR
            $table->boolean('referror')->default(0)->after('refqr'); // Mismatch flag
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['refslip', 'refqr', 'referror']);
        });
    }
};

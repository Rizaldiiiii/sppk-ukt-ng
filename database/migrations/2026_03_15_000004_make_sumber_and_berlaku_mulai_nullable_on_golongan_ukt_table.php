<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('golongan_ukt', function (Blueprint $table) {
            $table->string('sumber')->nullable()->change();
            $table->date('berlaku_mulai')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('golongan_ukt', function (Blueprint $table) {
            $table->string('sumber')->nullable(false)->change();
            $table->date('berlaku_mulai')->nullable(false)->change();
        });
    }
};

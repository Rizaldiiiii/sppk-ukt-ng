<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('konten_publik', 'visual_path')) {
            Schema::table('konten_publik', function (Blueprint $table) {
                $table->string('visual_path')->nullable()->after('isi');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('konten_publik', 'visual_path')) {
            Schema::table('konten_publik', function (Blueprint $table) {
                $table->dropColumn('visual_path');
            });
        }
    }
};

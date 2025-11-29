<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom status lama
            $table->dropColumn('status');
            
            // Tambah kolom active baru
            $table->boolean('active')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Untuk rollback: hapus active, tambah status
            $table->dropColumn('active');
            $table->string('status')->default('pending');
        });
    }
};
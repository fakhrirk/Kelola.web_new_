<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->unique()->nullable()->after('email');
            $table->enum('role', ['owner', 'admin', 'karyawan'])->default('karyawan')->after('password');
            $table->decimal('base_salary', 15, 2)->default(0)->after('role');
            $table->softDeletes(); // Untuk fitur hapus karyawan (opsional tapi bagus)
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'role', 'base_salary', 'deleted_at']);
        });
    }
};

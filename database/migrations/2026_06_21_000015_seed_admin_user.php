<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->insertOrIgnore([
            'name'       => 'Administrador',
            'email'      => 'admin@aquasense.com',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'admin@aquasense.com')->delete();
    }
};

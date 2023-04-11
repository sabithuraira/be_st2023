<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'kode_kab' => "00",
            'name' => "Admin",
            'email' => "admin@bpssumsel.com",
            'password' => '$2y$10$icvESUKYjqBrU3I.laBrJ.QiBVnnbx23vGiShWPOsUj2sZe56Jel6',
        ]);
        Role::create(['name' => 'PPL']);
        Role::create(['name' => 'PML']);
        Role::create(['name' => 'Koseka']);
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin Provinsi']);
        Role::create(['name' => 'Admin Kabupaten']);
    }
}

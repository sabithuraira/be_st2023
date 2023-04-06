<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        DB::table('users')->insert([
            'kode_kab' => "00",
            'name' => "Admin",
            'email' => "admin@bpssumsel.com",
            'password' => '$2y$10$icvESUKYjqBrU3I.laBrJ.QiBVnnbx23vGiShWPOsUj2sZe56Jel6',
        ]);
    }
}

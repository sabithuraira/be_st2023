<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilkerstatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = public_path('sql/kabs.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        $path = public_path('sql/kecs.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        $path = public_path('sql/desas.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        $path = public_path('sql/master_sls.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}

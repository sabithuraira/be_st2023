<?php

namespace Database\Seeders;

use App\Models\Ruta;
use App\Models\Sls;
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

        Sls::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "008",
            'id_sls' => "0001",
            'id_sub_sls' => "00",
            'nama_sls' => "RT 01 RW 01",
            'sls_op' => "1",
            'jenis_sls' => "1",
            'jml_art_tani' => "20",
            'jml_keluarga_tani' => "18",
            'sektor1' => "10",
            'sektor2' => "7",
            'sektor3' => "1",
            'sektor4' => "0",
            'sektor5' => "0",
            'sektor6' => "0",
            'jml_keluarga_tani' => "0",
            'jml_nr' => "0",
            'jml_dok_ke_pml' => "0",
            'jml_dok_ke_koseka' => "0",
            'status_selesai_pcl' => "0",
            'created_by' => "0",
            'updated_by' => "0",
        ]);

        Sls::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "008",
            'id_sls' => "0002",
            'id_sub_sls' => "00",
            'nama_sls' => "RT 12 RW 01",
            'sls_op' => "1",
            'jenis_sls' => "1",
            'jml_art_tani' => "22",
            'jml_keluarga_tani' => "20",
            'sektor1' => "9",
            'sektor2' => "6",
            'sektor3' => "2",
            'sektor4' => "1",
            'sektor5' => "1",
            'sektor6' => "1",
            'jml_keluarga_tani' => "0",
            'jml_nr' => "0",
            'jml_dok_ke_pml' => "0",
            'jml_dok_ke_koseka' => "0",
            'status_selesai_pcl' => "0",
            'created_by' => "0",
            'updated_by' => "0",
        ]);

        Sls::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "009",
            'id_sls' => "0001",
            'id_sub_sls' => "00",
            'nama_sls' => "RT 01 RW 01",
            'sls_op' => "1",
            'jenis_sls' => "1",
            'jml_art_tani' => "20",
            'jml_keluarga_tani' => "18",
            'sektor1' => "10",
            'sektor2' => "7",
            'sektor3' => "1",
            'sektor4' => "0",
            'sektor5' => "0",
            'sektor6' => "0",
            'jml_keluarga_tani' => "0",
            'jml_nr' => "0",
            'jml_dok_ke_pml' => "0",
            'jml_dok_ke_koseka' => "0",
            'status_selesai_pcl' => "0",
            'created_by' => "0",
            'updated_by' => "0",
        ]);


        Ruta::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "008",
            'id_sls' => "0001",
            'id_sub_sls' => "00",
            'nurt' => 1,
            'kepala_ruta' => "kharis",
            'sektor' => 1,
            'created_by' => "0",
            'updated_by' => "0",
        ]);
        Ruta::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "008",
            'id_sls' => "0001",
            'id_sub_sls' => "00",
            'nurt' => 2,
            'kepala_ruta' => "ari",
            'sektor' => 2,
            'created_by' => "0",
            'updated_by' => "0",
        ]);
        Ruta::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "008",
            'id_sls' => "0002",
            'id_sub_sls' => "00",
            'nurt' => 1,
            'kepala_ruta' => "bombom",
            'sektor' => 1,
            'created_by' => "0",
            'updated_by' => "0",
        ]);
        Ruta::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "008",
            'id_sls' => "0002",
            'id_sub_sls' => "00",
            'nurt' => 2,
            'kepala_ruta' => "jonathan",
            'sektor' => 3,
            'created_by' => "0",
            'updated_by' => "0",
        ]);
        Ruta::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "009",
            'id_sls' => "0001",
            'id_sub_sls' => "00",
            'nurt' => 1,
            'kepala_ruta' => "ade",
            'sektor' => 1,
            'created_by' => "0",
            'updated_by' => "0",
        ]);
        Ruta::create([
            'kode_prov' => "16",
            'kode_kab' => "71",
            'kode_kec' => "080",
            'kode_desa' => "009",
            'id_sls' => "0001",
            'id_sub_sls' => "00",
            'nurt' => 2,
            'kepala_ruta' => "hero",
            'sektor' => 3,
            'created_by' => "0",
            'updated_by' => "0",
        ]);
    }
}

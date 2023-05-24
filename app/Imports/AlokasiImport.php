<?php

namespace App\Imports;

use App\Models\Sls;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;

class AlokasiImport implements ToModel, WithUpserts
{
    public function startRow(): int
    {
        return 2;
    }

    public function uniqueBy()
    {
        return ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls'];
    }

    public function model(array $row)
    {
        $auth = Auth::user();

        if (strlen($row[1]) != 2) {
            return null;
        }

        $sls = Sls::where('kode_kab', $row[1])
            ->where('kode_kec', $row[2])
            ->where('kode_desa', $row[3])
            ->where('id_sls', $row[4])
            ->where('id_sub_sls', $row[5])
            ->first();

        if ($sls) {
            // jika sudah ada di db
            $kode_pcl = "";
            $kode_pml = "";
            $kode_koseka = "";
            $pcl = User::where('email', $row[8])->first();
            if ($pcl) {
                $kode_pcl = $row[8];
            }
            $pml = User::where('email', $row[9])->first();
            if ($pml) {
                $kode_pml = $row[9];
            }
            $koseka = User::where('email', $row[10])->first();
            if ($koseka) {
                $kode_koseka = $row[10];
            } else {
                $user = User::create([
                    'name'     =>  str_replace("@bps.go.id", "", $row[10]),
                    'email'    => $row[10],
                    'password' => Hash::make('123456'),
                    'kode_kab' => $row[1],
                    'kode_kec' => $row[2],
                    'kode_desa' => $row[3],
                    'created_by' => $auth->id
                ]);
                $user->syncRoles(["Koseka"]);
                $kode_koseka = $row[10];
            }

            $jumlah_keluarga_tani = 0;
            if ($row[7] != "") $jumlah_keluarga_tani = $row[7];
            $sls->jml_keluarga_tani = $jumlah_keluarga_tani;

            $sls->kode_pcl = $kode_pcl;
            $sls->kode_pml = $kode_pml;
            $sls->kode_koseka = $kode_koseka;
            return $sls;
        } else {
            $kode_pcl = "";
            $kode_pml = "";
            $kode_koseka = "";
            $pcl = User::where('email', $row[8])->first();
            if ($pcl) {
                $kode_pcl = $row[8];
            }
            $pml = User::where('email', $row[9])->first();
            if ($pml) {
                $kode_pml = $row[9];
            }
            $koseka = User::where('email', $row[10])->first();
            if ($koseka) {
                $kode_koseka = $row[10];
            } else {
                $user = User::create([
                    'name'     =>  str_replace("@bps.go.id", "", $row[10]),
                    'email'    => $row[10],
                    'password' => Hash::make('123456'),
                    'kode_kab' => $row[1],
                    'kode_kec' => $row[2],
                    'kode_desa' => $row[3],
                    'created_by' => $auth->id
                ]);
                $user->syncRoles(["Koseka"]);
                $kode_koseka = $row[10];
            }

            $jumlah_keluarga_tani = 0;
            if ($row[7] != "") $jumlah_keluarga_tani = $row[7];
            $sls->jml_keluarga_tani = $jumlah_keluarga_tani;
            $sls = Sls::create([
                'kode_prov'     => "16",
                'kode_kab' => $row[1],
                'kode_kec' => $row[2],
                'kode_desa' => $row[3],
                'id_sls' => $row[4],
                'id_sub_sls' => $row[5],
                'jml_keluarga_tani' => $jumlah_keluarga_tani,
                'kode_pcl' => $kode_pcl,
                'kode_pml' => $kode_pml,
                'kode_koseka' => $kode_koseka,
                'created_by' => $auth->id,
                'updated_by' => $auth->id,
            ]);

            return $sls;
        }
    }
}

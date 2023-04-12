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
        return 'id_sls';
    }

    public function model(array $row)
    {
        $auth = Auth::user();
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

            $pcl = User::where('email', $row[6])->first();
            if ($pcl) {
                $kode_pcl = $row[6];
            }

            $pml = User::where('email', $row[7])->first();
            if ($pml) {
                $kode_pcl = $row[7];
            }

            $koseka = User::where('email', $row[8])->first();
            if ($koseka) {
                $kode_pcl = $row[8];
            }

            $input = new Sls([
                'kode_kab' => $row[1],
                'kode_kec' => $row[2],
                'kode_desa' => $row[3],
                'id_sls' => $row[4],
                'id_sub_sls' => $row[5],
                'kode_pcl' => $kode_pcl,
                'kode_pml' => $kode_pml,
                'kode_koseka' => $kode_koseka,
            ]);
        }
        return $input;
    }
}

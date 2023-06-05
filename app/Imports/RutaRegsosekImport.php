<?php

namespace App\Imports;

use App\Models\Sls;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;

class RutaRegsosekImport implements ToModel, WithUpserts
{
    /**
     * @param Collection $collection
     */
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
        //
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
            $jumlah_keluarga_tani = 0;
            if ($row[7] != "") $jumlah_keluarga_tani = $row[7];
            $sls->jml_keluarga_tani = $jumlah_keluarga_tani;
            return $sls;
        } else {
            return null;
        }
    }
}

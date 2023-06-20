<?php

namespace App\Imports;

use App\Models\Sls;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class PrelistImport implements ToModel, WithUpserts, WithStartRow
{
    public function startRow(): int
    {
        return 20;
    }

    public function uniqueBy()
    {
        return ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls'];
    }

    public function model(array $row)
    {
        $auth = Auth::user();

        $id_sls = explode(' ', $row[9]);
        // dd($row);

        $sls = Sls::where('kode_kab', $row[2])
            ->where('kode_kec', $row[4])
            ->where('kode_desa', $row[6])
            ->where('id_sls', $id_sls[0])
            ->where('id_sub_sls', $id_sls[1])
            ->first();

        if ($sls) {
            $jumlah_keluarga = 0;
            $jumlah_keluarga_tani = 0;
            if ($row[14] != "") $jumlah_keluarga = $row[14];
            if ($row[15] != "") $jumlah_keluarga_tani = $row[15];
            $sls->ruta_prelist = $jumlah_keluarga;
            $sls->prelist_ruta_tani = $jumlah_keluarga_tani;

            return $sls;
        } else {
            $jumlah_keluarga = 0;
            if ($row[14] != "") $jumlah_keluarga = $row[14];
            $jumlah_keluarga_tani = 0;
            if ($row[15] != "") $jumlah_keluarga_tani = $row[15];
            $sls = Sls::create([
                'kode_prov'     => "16",
                'kode_kab' => $row[2],
                'kode_kec' => $row[4],
                'kode_desa' => $row[6],
                'id_sls' => $id_sls[0],
                'id_sub_sls' => $id_sls[1],
                'nama_sls' => $row[10],
                'ruta_prelist' => $jumlah_keluarga,
                'prelist_ruta_tani' => $jumlah_keluarga_tani,
                'created_by' => $auth->id,
                'updated_by' => $auth->id,
            ]);
            return $sls;
        }
    }
}

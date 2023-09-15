<?php

namespace App\Imports;

use App\Models\PesSt2023;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class PesImport implements ToModel, WithUpserts, WithStartRow
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
        $id_sls = explode(' ', $row[5]);
        $pes = PesSt2023::where('kode_kab', $row[2])
            ->where('kode_kec', $row[3])
            ->where('kode_desa', $row[4])
            ->where('id_sls', substr($row[5], 0, 4))
            ->where('id_sub_sls', substr($row[5], 4, 2))
            ->first();
        // dd(substr($row[5], 4, 2));
        if ($pes) {
            if ($row[8] != "")  $pes->klasifikasi = $row[7];
            if ($row[9] != "")  $pes->kode_ppl = $row[9];
            if ($row[10] != "")  $pes->nama_ppl = $row[10];
            if ($row[11] != "")  $pes->wa_ppl = $row[11];
            if ($row[12] != "")  $pes->kode_pml = $row[12];
            if ($row[13] != "")  $pes->nama_pml = $row[13];
            if ($row[14] != "")  $pes->wa_pml = $row[14];
            return $pes;
        }
    }
}

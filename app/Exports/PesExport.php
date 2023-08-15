<?php

namespace App\Exports;

use App\Models\PesSt2023;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PesExport implements FromCollection, WithHeadings, WithMapping
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        $condition = [];
        if (isset($this->request->kode_kab) && strlen($this->request->kode_kab) > 0) $condition[] = ['users.kode_kab', '=', $this->request->kode_kab];
        if (isset($this->request->kode_kec) && strlen($this->request->kode_kec) > 0) $condition[] = ['kode_kec', '=', $this->request->kode_kec];

        return PesSt2023::where($condition)->get();
    }

    public function map($sls): array
    {

        return [
            $sls->kode_prov,
            $sls->kode_kab,
            $sls->kode_kec,
            $sls->kode_desa,
            $sls->id_sls,
            $sls->id_sub_sls,
            $sls->kode_prov . $sls->kode_kab . $sls->kode_kec . $sls->kode_desa . $sls->id_sls . $sls->id_sub_sls,
            $sls->nama_sls,
            $sls->jml_ruta_tani,
            $sls->jml_art_tani,
            $sls->jml_ruta_pes,
            $sls->jml_art_pes,
            $sls->status_selesai,
            $sls->nama_ppl,
            $sls->nama_pml,
            $sls->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'kode_prov',
            'kode_kab',
            'kode_kec',
            'kode_desa',
            'kd_sls',
            'kd_sub_sls',
            'id_sls',
            'nama_sls',
            'jml_ruta_tani',
            'jml_art_tani',
            'jml_ruta_pes',
            'jml_art_pes',
            'status_selesai',
            'nama_ppl',
            'nama_pml',
            'updated_at'
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Sls;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlokasiExport implements FromCollection, WithMapping, WithHeadings
{

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return Sls::where('kode_kab', "LIKE", "%" . $this->request->kode_kab . "%")
            ->where('kode_kec', "LIKE", "%" . $this->request->kode_kec . "%")
            ->where('kode_desa', "LIKE", "%" . $this->request->kode_desa . "%")
            ->where('id_sls', "LIKE", "%" . $this->request->id_sls . "%")
            ->where('id_sub_sls', "LIKE", "%" . $this->request->id_sub_sls . "%")
            ->get();
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
            $sls->nama_sls,
            $sls->ruta_prelist,
            $sls->prelist_ruta_tani,
            $sls->kode_pcl,
            $sls->kode_pml,
            $sls->kode_koseka,
        ];
    }
    public function headings(): array
    {
        return [
            'kode_prov',
            'kode_kab',
            'kode_kec',
            'kode_desa',
            'id_sls',
            'id_sub_sls',
            'nama_sls',
            'prelist_ruta',
            'prelist_ruta_tani',
            'kode_pcl',
            'kode_pml',
            'kode_koseka',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Ruta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RutaExport implements FromCollection, WithMapping, WithHeadings
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        return Ruta::where('kode_kab', "LIKE", "%" . $this->request->kode_kab . "%")
            ->where('kode_kec', "LIKE", "%" . $this->request->kode_kec . "%")
            ->where('kode_desa', "LIKE", "%" . $this->request->kode_desa . "%")
            ->where('id_sls', "LIKE", "%" . $this->request->id_sls . "%")
            ->get();
    }
    public function map($ruta): array
    {
        return [
            $ruta->kode_prov,
            $ruta->kode_kab,
            $ruta->kode_kec,
            $ruta->kode_desa,
            $ruta->id_sls,
            $ruta->id_sub_sls,
            $ruta->nurt,
            $ruta->kepala_ruta,
            $ruta->start_time,
            $ruta->end_time,
            $ruta->start_latitude,
            $ruta->end_latitude,
            $ruta->start_longitude,
            $ruta->end_longitude,
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
            'nurt',
            'kepala_ruta',
            'start_time',
            'end_time',
            'start_latitude',
            'end_latutide',
            'start_longitude',
            'end_longitude'
        ];
    }
}

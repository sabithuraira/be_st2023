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
        $id_sls = substr($this->request->id_sls, 0, 4);
        $id_sub_sls = substr($this->request->id_sls, 4, 2);

        return Ruta::select(
            'ruta.kode_prov',
            'ruta.kode_kab',
            'ruta.kode_kec',
            'ruta.kode_desa',
            'ruta.id_sls',
            'ruta.id_sub_sls',
            'nurt',
            'kepala_ruta',
            'start_time',
            'end_time',
            'start_latitude',
            'start_latitude',
            'end_latitude',
            'start_longitude',
            'end_longitude',
            'subsektor1_a',
            'subsektor1_b',
            'subsektor2_a',
            'subsektor2_b',
            'subsektor3_a',
            'subsektor3_b',
            'subsektor4_a',
            'subsektor4_b',
            'subsektor4_c',
            'subsektor5_a',
            'subsektor5_b',
            'subsektor5_c',
            'subsektor6_a',
            'subsektor6_b',
            'subsektor6_c',
            'subsektor7_a',
            'jumlah_art',
            'jumlah_unit_usaha',
        )
            ->where('ruta.kode_kab', "LIKE", "%" . $this->request->kode_kab . "%")
            ->where('ruta.kode_kec', "LIKE", "%" . $this->request->kode_kec . "%")
            ->where('ruta.kode_desa', "LIKE", "%" . $this->request->kode_desa . "%")
            ->where('ruta.id_sls', "LIKE", "%" . $id_sls . "%")
            ->where('ruta.id_sub_sls', "LIKE", "%" . $id_sub_sls . "%")
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
            $ruta->sls->kode_pcl,
            $ruta->sls->kode_pml,
            $ruta->sls->kode_koseka,
            $ruta->start_time,
            $ruta->end_time,
            $ruta->start_latitude,
            $ruta->end_latitude,
            $ruta->start_longitude,
            $ruta->end_longitude,
            $ruta->subsektor1_a,
            $ruta->subsektor1_b,
            $ruta->subsektor2_a,
            $ruta->subsektor2_b,
            $ruta->subsektor3_a,
            $ruta->subsektor3_b,
            $ruta->subsektor4_a,
            $ruta->subsektor4_b,
            $ruta->subsektor4_c,
            $ruta->subsektor5_a,
            $ruta->subsektor5_b,
            $ruta->subsektor5_c,
            $ruta->subsektor6_a,
            $ruta->subsektor6_b,
            $ruta->subsektor6_c,
            $ruta->subsektor7_a,
            $ruta->jumlah_art,
            $ruta->jumlah_unit_usaha,
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
            'kode_pcl',
            'kode_pml',
            'kode_koseka',
            'start_time',
            'end_time',
            'start_latitude',
            'end_latutide',
            'start_longitude',
            'end_longitude',
            'subsektor1_a',
            'subsektor1_b',
            'subsektor2_a',
            'subsektor2_b',
            'subsektor3_a',
            'subsektor3_b',
            'subsektor4_a',
            'subsektor4_b',
            'subsektor4_c',
            'subsektor5_a',
            'subsektor5_b',
            'subsektor5_c',
            'subsektor6_a',
            'subsektor6_b',
            'subsektor6_c',
            'subsektor7_a',
            'jumlah_art',
            'jumlah_unit_usaha',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UmkmExport implements FromCollection, WithMapping, WithHeadings
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        if ($this->request->desa_filter) {
            $data = Umkm::select(
                'kode_kab as kode_kab',
                'kode_kec as kode_kec',
                'kode_desa as kode_desa',
                DB::raw(
                    "CONCAT(id_sls,id_sub_sls) as kode_sls, CONCAT(id_sls,id_sub_sls) as kode_wilayah"
                ),
                'nama_sls as nama_wilayah',
                DB::raw(
                    "1 as jml_sls"
                ),
                'status_selesai as status_selesai',
                'jml_kk as jml_kk',
                'no_urut_usaha_terbesar as jml_usaha',
                'jml_koperasi as jml_koperasi'
            )
                ->where('kode_kab', $this->request->kab_filter)
                ->where('kode_kec', $this->request->kec_filter)
                ->where('kode_desa', $this->request->desa_filter)

                ->get();
        } else if ($this->request->kec_filter) {
            $data = Umkm::where('kode_kab', $this->request->kab_filter)
                ->where('kode_kec', $this->request->kec_filter)
                ->where('id_kab', $this->request->kab_filter)
                ->where('id_kec', $this->request->kec_filter)
                ->groupBy('kode_kab', 'kode_kec', 'kode_desa', 'nama_desa')
                ->selectRaw('
                 kode_kab as kode_kab,
                 kode_kec as kode_kec,
                 kode_desa as kode_desa,
                 kode_desa as kode_wilayah,
                 desas.nama_desa as nama_wilayah,
                 count(*) as jml_sls,
                 sum(status_selesai) as status_selesai,
                 sum(jml_kk) as jml_kk,
                 sum(no_urut_usaha_terbesar) as jml_usaha,
                 sum(jml_koperasi) as jml_koperasi
                 ')
                ->join('desas', 'sls_umkm.kode_desa', '=', 'desas.id_desa')
                ->get();
        } else if ($this->request->kab_filter) {
            $data = Umkm::where('kode_kab', $this->request->kab_filter)
                ->where('id_kab', $this->request->kab_filter)
                ->groupBy('kode_kab', 'kode_kec', 'nama_kec')
                ->selectRaw('
                 kode_kab as kode_kab,
                 kode_kec as kode_kec,
                 kode_kec as kode_wilayah,
                 kecs.nama_kec as nama_wilayah,
                 count(*) as jml_sls,
                 sum(status_selesai) as status_selesai,
                 sum(jml_kk) as jml_kk,
                 sum(no_urut_usaha_terbesar) as jml_usaha,
                 sum(jml_koperasi) as jml_koperasi
                 ')
                ->join('kecs', 'sls_umkm.kode_kec', '=', 'kecs.id_kec')
                ->get();
        } else {
            $data = Umkm::groupBy('kode_kab', 'nama_kab')
                ->selectRaw('
                 kode_kab as kode_kab,
                 kode_kab as kode_wilayah,
                 kabs.nama_kab as nama_wilayah,
                 count(*) as jml_sls,
                 sum(status_selesai) as status_selesai,
                 sum(jml_kk) as jml_kk,
                 sum(no_urut_usaha_terbesar) as jml_usaha,
                 sum(jml_koperasi) as jml_koperasi
                 ')
                ->join('kabs', 'sls_umkm.kode_kab', '=', 'kabs.id_kab')
                ->get();
        }
        return $data;
    }

    public function map($data): array
    {
        $persen = 0;
        if ($data->jml_sls) {
            $persen = round($data->status_selesai / $data->jml_sls, 2);
        }

        return [
            $data->kode_wilayah,
            $data->nama_wilayah,
            $data->jml_sls,
            $data->status_selesai,
            $persen,
            $data->jml_kk,
            $data->jml_usaha,
            $data->jml_koperasi
        ];
    }
    public function headings(): array
    {
        return [
            'Kode Wilayah',
            'Nama Wilayah',
            'Jumlah SLS',
            'Selesai',
            'Persentase Selesai',
            'Jumlah KK',
            'Jumlah Usaha',
            'Jumlah Koperasi',
        ];
    }
}

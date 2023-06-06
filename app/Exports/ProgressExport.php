<?php

namespace App\Exports;

use App\Models\Desas;
use App\Models\Kabs;
use App\Models\Kecs;
use App\Models\Sls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProgressExport implements FromCollection,  WithMapping, WithHeadings
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        if ($this->request->desa_filter) {
            $data = Sls::select(
                DB::raw("CONCAT(id_sls, id_sub_sls) AS id_sls, CONCAT(id_sls, id_sub_sls) AS kode_wilayah, nama_sls as nama_wilayah, '1' as jumlah"),
                'status_selesai_pcl as selesai',
                'jml_keluarga_tani as  perkiraan_ruta',
            )
                ->withCount('ruta as ruta_selesai')
                ->where('kode_kab', $this->request->kab_filter)
                ->where('kode_kec', $this->request->kec_filter)
                ->where('kode_desa', $this->request->desa_filter)
                ->get();
        } else if ($this->request->kec_filter) {
            $data = Desas::select(
                'id_desa as kode_desa',
                'id_desa as kode_wilayah',
                'nama_desa as nama_wilayah'
            )->withCount('sls as jumlah')
                ->withSum('sls as selesai', 'status_selesai_pcl')
                ->withSum('sls as perkiraan_ruta', 'jml_keluarga_tani')
                ->withCount('ruta as ruta_selesai')
                ->where('id_kab', $this->request->kab_filter)
                ->where('id_kec', $this->request->kec_filter)
                ->get();
        } else if ($this->request->kab_filter) {
            $data = Kecs::select(
                'id_kec as kode_kec',
                'id_kec as kode_wilayah',
                'nama_kec as nama_wilayah'
            )->withCount('sls as jumlah')
                ->withSum('sls as selesai', 'status_selesai_pcl')
                ->withSum('sls as perkiraan_ruta', 'jml_keluarga_tani')
                ->withCount('ruta as ruta_selesai')
                ->where('id_kab', $this->request->kab_filter)
                ->orderby('id_kec')
                ->get();
        } else {
            $data = Kabs::select(
                'id_kab as kode_kab',
                'id_kab as kode_wilayah',
                'nama_kab as nama_wilayah',
                'alias'
            )->withCount('sls as jumlah')
                ->withSum('sls as selesai', 'status_selesai_pcl')
                ->withSum('sls as perkiraan_ruta', 'jml_keluarga_tani')
                ->withCount('ruta as ruta_selesai')
                ->get();
        }
        return $data;
    }

    public function map($data): array
    {
        $persen = 0;
        if ($data->jumlah) {
            $persen = round($data->selesai / $data->jumlah, 3);
        }
        return [
            $data->kode_wilayah,
            $data->nama_wilayah,
            $data->jumlah,
            $data->selesai,
            $persen,
            $data->perkiraan_ruta,
            $data->ruta_selesai,
        ];
    }
    public function headings(): array
    {
        return [
            'kode_wilayah',
            'nama_wilayah',
            'jumlah',
            'selesai',
            'persentase',
            'perkiraan_ruta_tani',
            'ruta_tani_pencacahan'
        ];
    }
}

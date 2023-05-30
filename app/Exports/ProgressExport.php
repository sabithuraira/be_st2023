<?php

namespace App\Exports;

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
                'master_sls.kode_kab',
                'nama_kab',
                'master_sls.kode_kec',
                'nama_kec',
                'master_sls.kode_desa',
                'nama_desa',
                'master_sls.id_sls',
                'master_sls.id_sls as kode_wilayah',
                'nama_sls as nama_wilayah',
                DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah, COUNT(ruta.id) as ruta_selesai, SUM(master_sls.jml_keluarga_tani) as perkiraan_ruta')
            )
                ->leftJoin('desas', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'desas.id_kab')
                        ->on('master_sls.kode_kec', '=', 'desas.id_kec')
                        ->on('master_sls.kode_desa', '=', 'desas.id_desa');
                })
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->leftjoin('ruta', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->where('master_sls.kode_kab', $this->request->kab_filter)
                ->where('master_sls.kode_kec', $this->request->kec_filter)
                ->where('master_sls.kode_desa', $this->request->desa_filter)
                ->groupby('kode_kab', 'nama_kab', 'kode_kec', 'nama_kec', 'kode_desa', 'nama_desa', 'id_sls', 'nama_sls')
                ->orderBy('id_sls', 'asc')
                ->get();
        } else if ($this->request->kec_filter) {
            $data = Sls::select(
                'master_sls.kode_kab',
                'nama_kab',
                'master_sls.kode_kec',
                'nama_kec',
                'master_sls.kode_desa',
                'master_sls.kode_desa as kode_wilayah',
                'nama_desa as nama_wilayah',
                DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah,  COUNT(ruta.id) as ruta_selesai, SUM(master_sls.jml_keluarga_tani) as perkiraan_ruta')
            )
                ->leftJoin('desas', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'desas.id_kab')
                        ->on('master_sls.kode_kec', '=', 'desas.id_kec')
                        ->on('master_sls.kode_desa', '=', 'desas.id_desa');
                })
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->leftjoin('ruta', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->where('master_sls.kode_kab', $this->request->kab_filter)
                ->where('master_sls.kode_kec', $this->request->kec_filter)
                ->groupby('kode_kab', 'nama_kab', 'kode_kec', 'nama_kec', 'kode_desa', 'nama_desa')
                ->orderBy('kode_desa', 'asc')
                ->get();
        } else if ($this->request->kab_filter) {
            $data = Sls::select(
                'master_sls.kode_kab',
                'nama_kab',
                'master_sls.kode_kec',
                'master_sls.kode_kec as kode_wilayah',
                'nama_kec as nama_wilayah',
                DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah,  COUNT(ruta.id) as ruta_selesai, SUM(master_sls.jml_keluarga_tani) as perkiraan_ruta')
            )
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->leftjoin('ruta', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->where('master_sls.kode_kab', $this->request->kab_filter)
                ->groupby('kode_kab', 'nama_kab', 'kode_kec', 'nama_kec')
                ->orderBy('kode_kec', 'asc')
                ->get();
        } else {
            $data = Sls::select(
                'master_sls.kode_kab',
                'master_sls.kode_kab as kode_wilayah',
                'nama_kab as nama_wilayah',
                'alias',
                DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah, COUNT(ruta.id) as ruta_selesai, SUM(master_sls.jml_keluarga_tani) as perkiraan_ruta')
            )
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->leftjoin('ruta', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->groupby('kode_kab', 'nama_kab', 'alias')
                ->orderBy('kode_kab', 'asc')
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

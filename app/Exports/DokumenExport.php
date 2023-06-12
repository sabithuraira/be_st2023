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

class DokumenExport implements FromCollection, WithMapping, WithHeadings
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        //
        if ($this->request->desa_filter) {
            $data = Sls::select(
                'kode_kab',
                'kode_kec',
                'kode_desa',
                DB::raw("CONCAT(id_sls, id_sub_sls) AS id_sls,
                 CONCAT(id_sls, id_sub_sls) AS kode_wilayah,
                 nama_sls as nama_wilayah,
                 sum(jml_dok_ke_pml) as dok_pml,
                 sum(jml_dok_ke_koseka) as dok_koseka,
                 sum(jml_nr) as jml_nr,
                 sum(jml_dok_ke_bps) as dok_bps
                 "),
                'kode_pcl',
                'kode_pml',
                'kode_koseka'
            )->withCount('ruta as dok_pcl')
                ->where('kode_kab', $this->request->kab_filter)
                ->where('kode_kec', $this->request->kec_filter)
                ->where('kode_desa', $this->request->desa_filter)
                ->groupby(
                    'kode_kab',
                    'kode_kec',
                    'kode_desa',
                    'id_sls',
                    'id_sub_sls',
                    'nama_sls',
                    'kode_pcl',
                    'kode_pml',
                    'kode_koseka'
                )
                ->get();
        } else if ($this->request->kec_filter) {
            $data = Desas::select(
                'id_kab as kode_kab',
                'id_kec as kode_kec',
                'id_desa as kode_desa',
                'id_desa as kode_wilayah',
                'nama_desa as nama_wilayah'
            )->withCount('ruta as dok_pcl')
                ->withSum('sls as dok_pml', 'jml_dok_ke_pml')
                ->withSum('sls as dok_koseka', 'jml_dok_ke_koseka')
                ->withSum('sls as jml_nr', 'jml_nr')
                ->withSum('sls as dok_bps', 'jml_dok_ke_bps')
                ->where('id_kab', $this->request->kab_filter)
                ->where('id_kec', $this->request->kec_filter)
                ->get();
        } else if ($this->request->kab_filter) {

            $data = Kecs::select(
                'id_kab as kode_kab',
                'id_kec as kode_kec',
                'id_kec as kode_wilayah',
                'nama_kec as nama_wilayah'
            )->withCount('ruta as dok_pcl')
                ->withSum('sls as dok_pml', 'jml_dok_ke_pml')
                ->withSum('sls as dok_koseka', 'jml_dok_ke_koseka')
                ->withSum('sls as jml_nr', 'jml_nr')
                ->withSum('sls as dok_bps', 'jml_dok_ke_bps')
                ->where('id_kab', $this->request->kab_filter)
                ->orderby('id_kec')
                ->get();
        } else {
            $data = Kabs::select(
                'id_kab as kode_kab',
                'id_kab as kode_wilayah',
                'nama_kab as nama_wilayah',
                'alias'
            )->withCount('ruta as dok_pcl')
                ->withSum('sls as dok_pml', 'jml_dok_ke_pml')
                ->withSum('sls as dok_koseka', 'jml_dok_ke_koseka')
                ->withSum('sls as jml_nr', 'jml_nr')
                ->withSum('sls as dok_bps', 'jml_dok_ke_bps')
                ->get();
        }
        return $data;
    }

    public function map($data): array
    {

        if (!$this->request->desa_filter) {
            return [
                $data->kode_wilayah,
                $data->nama_wilayah,
                $data->dok_pcl,
                $data->dok_pml,
                $data->dok_koseka,
                $data->dok_bps,
            ];
        } else {
            return [
                $data->kode_wilayah,
                $data->nama_wilayah,
                $data->kode_pcl,
                $data->dok_pcl,
                $data->kode_pml,
                $data->dok_pml,
                $data->kode_koseka,
                $data->dok_koseka,
            ];
        }
    }
    public function headings(): array
    {

        if (!$this->request->desa_filter) {
            return [
                'Kode Wilayah',
                'Nama Wilayah',
                'Dokumen PCL',
                'Dokumen PML',
                'Dokumen Koseka',
                'Dokumen Telah di Kantor BPS'
            ];
        } else {

            return [
                'Kode Wilayah',
                'Nama Wilayah',
                'Kode PCL',
                'Dokumen PCL',
                'kode PML',
                'Dokumen PML',
                'Kode Koseka',
                'Dokumen Koseka',
            ];
        }
    }
}

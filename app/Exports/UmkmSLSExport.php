<?php

namespace App\Exports;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UmkmSLSExport implements FromCollection, WithMapping, WithHeadings
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        if (isset($this->request->kab_filter) && strlen($this->request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $this->request->kab_filter];
        if (isset($this->request->kec_filter) && strlen($this->request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $this->request->kec_filter];
        if (isset($this->request->desa_filter) && strlen($this->request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $this->request->desa_filter];
        // dd($condition);
        $data = Umkm::where($condition)->get();
        return $data;
    }
    public function map($data): array
    {
        return [
            $data->kode_prov,
            $data->kode_kab,
            $data->kode_kec,
            $data->kode_desa,
            $data->id_sls,
            $data->id_sub_sls,
            $data->nama_sls,
            $data->jml_kk,
            $data->no_urut_usaha_terbesar,
            $data->jml_koperasi,
            $data->status_selesai,
            $data->updated_at,
        ];
    }
    public function headings(): array
    {
        return [
            'Prov',
            'Kab',
            'Kec',
            'Desa',
            'Id SLS',
            'Id Sub SLS',
            'Nama SLS',
            'Jml KK',
            'Jml Usaha',
            'Jml Koperasi',
            'Status Selesai',
            'Waktu Update',
        ];
    }
}

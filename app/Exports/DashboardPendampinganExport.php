<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DashboardPendampinganExport implements FromCollection, WithHeadings, WithMapping
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
        if (isset($this->request->kode_desa) && strlen($this->request->kode_desa) > 0) $condition[] = ['kode_desa', '=', $this->request->kode_desa];
    
        $datas = User::role('PPL')
            ->withCount('sls_ppl as jml_sls')
            ->with(['p_pml' => function ($query) {
                $query->groupBy('kode_pcl', 'kode_pml')
                    ->select('kode_pcl', 'kode_pml', DB::raw('COUNT(pendampingan_pml) as pendampingan_pml'));
            }])
            ->with(['p_koseka' => function ($query) {
                $query->groupBy('kode_pcl', 'kode_koseka')
                    ->select('kode_pcl', 'kode_koseka', DB::raw('COUNT(pendampingan_koseka) as pendampingan_koseka'));
            }])
            ->where($condition)
            ->orderby('kode_kab')
            ->orderby('name')
            ->get();

        return $datas;
    }

    public function map($data): array
    {
        $rincian_pml = "";
        $rincian_koseka = "";

        foreach ($data['p_pml'] as $pml){
            $rincian_pml .= $pml['kode_pml'].": ".$pml['pendampingan_pml'];
        }

        foreach ($data['p_koseka'] as $koseka){
            $rincian_koseka .= $koseka['kode_koseka'].": ".$koseka['pendampingan_koseka'];
        }

        return [
            $data->kode_kab,
            $data->email,
            $data->name,
            $data->jml_sls,
            $rincian_pml,
            $rincian_koseka
        ];
    }
    public function headings(): array
    {
        return [
            'Kode Kab/Kota',
            'Email',
            'Nama Petugas',
            'Jumlah SLS',
            'Pendampingan PML',
            'Pendampingan KOSEKA'
        ];
    }
}

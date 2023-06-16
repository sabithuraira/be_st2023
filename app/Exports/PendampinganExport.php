<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PendampinganExport implements FromCollection, WithHeadings, WithMapping
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        if (isset($this->request->kode_kab) && strlen($this->request->kode_kab) > 0) $condition[] = ['users.kode_kab', '=', $this->request->kode_kab];
        if (isset($this->request->kode_kec) && strlen($this->request->kode_kec) > 0) $condition[] = ['users.kode_kec', '=', $this->request->kode_kec];
        if (isset($this->request->kode_desa) && strlen($this->request->kode_desa) > 0) $condition[] = ['users.kode_desa', '=', $this->request->kode_desa];
        $datas = [];
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

        return [
            $data->kode_kab,
            $data->name,
            $data->email,
            $data->jml_sls,
            $data->p_pml[0]['kode_pml'],
            $data->p_pml[0]['pendampingan_pml'],
            $data->p_pml[0]['kode_koseka'],
            $data->p_pml[0]['pendampingan_koseka']
        ];
    }
    public function headings(): array
    {
        return [
            'kode_kab',
            'nama',
            'email',
            'jml_sls',
            'kode_pml',
            'sls didampingi pml',
            'kode_koseka',
            'sls didampingi koseka',
        ];
    }
}

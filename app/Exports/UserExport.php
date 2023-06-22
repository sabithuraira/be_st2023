<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $datas = [];
        $condition[] = ['kode_kab', '<>', '00'];
        $keyword = $this->request->keyword;
        if (isset($this->request->kode_kab) && strlen($this->request->kode_kab) > 0) $condition[] = ['kode_kab', '=', $this->request->kode_kab];
        if (isset($this->request->kode_kec) && strlen($this->request->kode_kec) > 0) $condition[] = ['kode_kec', '=', $this->request->kode_kec];
        if (isset($this->request->kode_desa) && strlen($this->request->kode_desa) > 0) $condition[] = ['kode_desa', '=', $this->request->kode_desa];
        //KEYWORD CONDITION
        if (isset($this->request->keyword) && strlen($this->request->keyword) > 0) {
            $datas = User::where($condition)
                ->where(
                    (function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', '%' . $keyword . '%')
                            ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                    })
                )
                ->withcount('sls_ppl as jumlah_sls_ppl')
                ->withcount('sls_pml as jumlah_sls_pml')
                ->withcount('sls_koseka as jumlah_sls_koseka')
                ->with('roles')
                ->orderBy('kode_kab', 'Asc')->get();
        } else {
            $datas = User::where($condition)
                ->with('roles')
                ->withcount('sls_ppl as jumlah_sls_ppl')
                ->withcount('sls_pml as jumlah_sls_pml')
                ->withcount('sls_koseka as jumlah_sls_koseka')
                ->orderBy('kode_kab', 'Asc')
                ->orderBy('name', 'Asc')->get();
        }
        // $datas->get();
        return $datas;
    }

    public function map($data): array
    {

        $role = "";
        // dd($data->roles);
        if (count($data->roles) > 0) $role = $data->roles[0]['name'];

        $jumlah_sls = 0;
        if ($role) {
            if ($data->roles[0]['name'] == "PPL")
                $jumlah_sls = $data->jumlah_sls_ppl;
            elseif ($data->roles[0]['name'] == "PML")
                $jumlah_sls = $data->jumlah_sls_pml;
            elseif ($data->roles[0]['name'] == "Koseka")
                $jumlah_sls = $data->jumlah_sls_koseka;
        }

        return [
            $data->kode_kab,
            $data->kode_kec,
            $data->kode_desa,
            $data->name,
            $data->email,
            $role,
            $jumlah_sls
        ];
    }

    public function headings(): array
    {
        return [
            'kode_kab',
            'kode_kec',
            'kode_desa',
            'name',
            'email',
            'posisi',
            'alokasi_sls'
        ];
    }
}

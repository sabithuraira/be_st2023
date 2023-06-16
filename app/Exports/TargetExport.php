<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TargetExport implements FromCollection, WithMapping, WithHeadings
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        //$condition = [];
        $keyword = $this->request->keyword;
        $condition[] = ['kode_kab', '<>', '00'];

        if (isset($this->request->kab_filter) && strlen($this->request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $this->request->kab_filter];
        if (isset($this->request->kec_filter) && strlen($this->request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $this->request->kec_filter];
        if (isset($this->request->desa_filter) && strlen($this->request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $this->request->desa_filter];

        if (isset($this->request->keyword) && strlen($this->request->keyword) > 0) {
            $datas = User::where($condition)
                ->role(["PPL"])
                ->where(
                    (function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', '%' . $keyword . '%')
                            ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                    })
                )->with('roles')
                ->withCount('rutas')
                ->orderBy('kode_kab', 'Asc')
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $datas = User::where($condition)
                ->role(["PPL"])
                ->with('roles')
                ->withCount('rutas')
                ->orderBy('kode_kab', 'Asc')
                ->orderBy('name', 'Asc')
                ->get();
        }
        return $datas;
    }

    public function map($ruta): array
    {
        $target_hari_ini = 0;
        $date2 = date('Y-m-d');
        $date1 = strtotime("2023-06-01");
        $diff = round(abs($date1 - strtotime($date2)) / 86400);
        $target_hari_ini = 10 * ($diff + 1);
        $persen = "";
        if ($ruta->rutas_count < $target_hari_ini) {
            $persen = $ruta->rutas_count / $target_hari_ini * 100 . "%";
        }

        return [
            $ruta->kode_kab,
            $ruta->name,
            $ruta->email,
            $ruta->rutas_count,
            $persen,
        ];
    }
    public function headings(): array
    {
        return [
            'kode_kab',
            'nama',
            'email',
            'jumlah_ruta_selesai',
            'persentase_selesai_by_target',
        ];
    }
}

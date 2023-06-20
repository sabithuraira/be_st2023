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

        // $datas = User::where($condition)
        //     ->role(["PPL"])
        //     ->with('roles')
        //     ->withCount('rutas')
        //     ->orderBy('kode_kab', 'Asc')
        //     ->orderBy('name', 'Asc')
        //     ->get();
        // ///
        $datas = User::where($condition)
            ->role(["PPL"])
            ->with('roles')
            ->withCount('rutas')
            ->withCount('sls_ppl as jml_sls')
            ->withSum('sls_ppl as prelist_ruta', 'ruta_prelist')
            ->orderBy('kode_kab', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();
        //
        return $datas;
    }

    public function map($ruta): array
    {
        $target_hari_ini = 0;
        $date2 = date('Y-m-d');
        $date1 = strtotime("2023-06-01");
        $diff = round(abs($date1 - strtotime($date2)) / 86400);
        $target_hari_ini = 10 * ($diff + 1);

        $persen = '100 %';
        if ($ruta->prelist_ruta > 0) {
            // $persen = $ruta->rutas_count / $target_hari_ini * 100 . "%";
            $total = round(($ruta['rutas_count'] / $ruta['prelist_ruta']) * 100, 2);
            $persen = $total . " %";
        }

        return [
            $ruta->kode_kab,
            $ruta->name,
            $ruta->email,
            $ruta->jml_sls,
            $ruta->prelist_ruta,
            $ruta->rutas_count,
            $persen,
        ];
    }
    public function headings(): array
    {
        return [
            'Kode Kab/Kota',
            'Nama',
            'Email',
            'Jumlah SLS',
            'Target Ruta Prelist',
            'Ruta Dicacah',
            'Persentase Pencacahan/Prelist',
        ];
    }
}

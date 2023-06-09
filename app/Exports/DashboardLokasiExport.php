<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DashboardLokasiExport implements FromCollection, WithHeadings, WithMapping
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        $condition = [];
        $tanggal_awal = $this->request->tanggal_awal ? $this->request->tanggal_awal : now()->subDays(7)->format('d/m/y');
        $tanggal_akhir = $this->request->tanggal_akhir ? $this->request->tanggal_akhir : now()->format('d/m/y');
        $awalDate = \DateTime::createFromFormat('d/m/y', $tanggal_awal);
        $akhirDate = \DateTime::createFromFormat('d/m/y', $tanggal_akhir);
        $datas = User::where($condition)
            ->role(["PPL"])
            ->with(['rutas' => function ($query) {
                $query->select('created_by', DB::raw('AVG(ABS(start_latitude) - ABS(end_latitude)) as rata_latitude, AVG(ABS(start_longitude) - ABS(end_longitude)) as rata_longitude, COUNT(*) as jml_ruta'))
                    ->groupBy('created_by');
            }])
            ->orderBy('kode_kab', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();

        return $datas;
    }
    public function map($data): array
    {
        $jarak_latitude = "";
        // $dt['rutas'][0]['rata_latitude'];
        if (count($data['rutas']) > 0) {
            if ($data['rutas'][0]) {
                $jarak_latitude =  $data['rutas'][0]['rata_latitude'];
            }
        }
        $jarak_longitude = "";
        if (count($data['rutas']) > 0) {
            if ($data['rutas'][0]) {
                $jarak_longitude =  $data['rutas'][0]['rata_longitude'];
            }
        }

        $jml_ruta = "";
        if (count($data['rutas']) > 0) {
            if ($data['rutas'][0]) {
                $jml_ruta = $data['rutas'][0]['jml_ruta'];
            }
        }
        return [
            $data->kode_kab,
            $data->email,
            $data->name,
            $jarak_latitude,
            $jarak_longitude,
            $jml_ruta
        ];
    }
    public function headings(): array
    {
        return [
            'kode_kab',
            'email',
            'nama',
            'jarak_latitude',
            'jarak_longitude',
            'jml_ruta'
        ];
    }
}

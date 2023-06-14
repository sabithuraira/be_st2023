<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DashboardWaktuExport implements FromCollection, WithMapping, WithHeadings
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        $condition = [];
        if (isset($this->request->kode_kab) && strlen($this->request->kode_kab) > 0) $condition[] = ['kode_kab', '=', $this->request->kode_kab];
        if (isset($this->request->kode_kec) && strlen($this->request->kode_kec) > 0) $condition[] = ['kode_kec', '=', $this->request->kode_kec];
        if (isset($this->request->kode_desa) && strlen($this->request->kode_desa) > 0) $condition[] = ['kode_desa', '=', $this->request->kode_desa];
        $tanggal_awal = $this->request->tanggal_awal ? $this->request->tanggal_awal : now()->subDays(7)->format('m/d/Y');
        $tanggal_akhir = $this->request->tanggal_akhir ? $this->request->tanggal_akhir : now()->format('m/d/Y');
        $awalDate = \DateTime::createFromFormat('m/d/Y', $tanggal_awal);
        $akhirDate = \DateTime::createFromFormat('m/d/Y', $tanggal_akhir);
        $datas = User::where($condition)
            ->role(["PPL"])
            ->with('roles')
            ->with(['rutas' => function ($query) use ($awalDate, $akhirDate) {
                $query
                    ->whereBetween('created_at', [
                        $awalDate->format('Y-m-d 00:00:00'),
                        $akhirDate->format('Y-m-d 23:59:59')
                    ])
                    ->select('created_by', DB::raw('AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as rata_rata_waktu_menit, COUNT(*) as jml_ruta'))
                    ->groupBy('created_by');
            }])
            ->orderBy('kode_kab', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();

        return $datas;
    }

    public function map($data): array
    {
        $rata_rata_waktu_menit = "";
        if (count($data['rutas']) > 0) {
            if ($data['rutas'][0]) {
                $rata_rata_waktu_menit = $data['rutas'][0]['rata_rata_waktu_menit'];
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
            $rata_rata_waktu_menit,
            $jml_ruta
        ];
    }
    public function headings(): array
    {
        return [
            'kode_kab',
            'email',
            'nama',
            'rata_rata_waktu_menit',
            'jml_ruta'
        ];
    }
}

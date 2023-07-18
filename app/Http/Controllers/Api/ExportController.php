<?php

namespace App\Http\Controllers\Api;

use App\Exports\AlokasiExport;
use App\Exports\DashboardLokasiExport;
use App\Exports\DashboardWaktuExport;
use App\Exports\DashboardPendampinganExport;
use App\Exports\DokumenExport;
use App\Exports\PendampinganExport;
use App\Exports\ProgressExport;
use App\Exports\RutaExport;
use App\Exports\SlsPerubahanExport;
use App\Exports\TargetExport;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use Maatwebsite\Excel\Writer;
use Validator;

class ExportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/export_alokasi",
     *     tags={"export"},
     *     summary="Get .XLSX Alokasi",
     *     description="export Sls",
     *     operationId="export",
     *      @OA\Parameter(
     *          name="Bearer Token",
     *          description="",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="kode_kab",
     *          description="Kode Kabupaten 2 Digit",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="kode_kec",
     *          description="Kode Kecamtan 3 Digit",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="kode_desa",
     *          description="Kode desa 3 Digit",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="id_sls",
     *          description="Id SLS 4 Digit",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="id_sub_sls",
     *          description="Id Sub SLS 2 Digit",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Download excel alokasi SLS"
     *     )
     * )
     */
    public function export_alokasi(Request $request)
    {
        return Excel::download(new AlokasiExport($request), 'alokasi.xlsx');
    }

    public function export_ruta(Request $request)
    {
        // return excel::download(new RutaExport($request), 'ruta_16.xlsx');
        // return Excel::download(new RutaExport($request), 'ruta_16.csv', \Maatwebsite\Excel\Excel::CSV, [
        //     'Content-Type' => 'text/csv',
        // ]);
        // return Excel::download(new RutaExport($request), 'ruta_16.csv', \Maatwebsite\Excel\Excel::CSV);
        $file = fopen(storage_path('app/export.csv'), 'w');

        $header = [
            'kode_prov',
            'kode_kab',
            'kode_kec',
            'kode_desa',
            'id_sls',
            'id_sub_sls',
            'nurt',
            'kepala_ruta',
            'kode_pcl',
            'kode_pml',
            'kode_koseka',
            'start_time',
            'end_time',
            'start_latitude',
            'end_latutide',
            'start_longitude',
            'end_longitude',
            'subsektor1_a',
            'subsektor1_b',
            'subsektor2_a',
            'subsektor2_b',
            'subsektor3_a',
            'subsektor3_b',
            'subsektor4_a',
            'subsektor4_b',
            'subsektor4_c',
            'subsektor5_a',
            'subsektor5_b',
            'subsektor5_c',
            'subsektor6_a',
            'subsektor6_b',
            'subsektor6_c',
            'subsektor7_a',
            'jumlah_art',
            'jumlah_unit_usaha',
        ];

        fputcsv($file, $header);
        $id_sls = substr($request->id_sls, 0, 4);
        $id_sub_sls = substr($request->id_sls, 4, 2);
        $data = Ruta::select(
            'ruta.kode_prov',
            'ruta.kode_kab',
            'ruta.kode_kec',
            'ruta.kode_desa',
            'ruta.id_sls',
            'ruta.id_sub_sls',
            'nurt',
            'kepala_ruta',
            'start_time',
            'end_time',
            'start_latitude',
            'start_latitude',
            'end_latitude',
            'start_longitude',
            'end_longitude',
            'subsektor1_a',
            'subsektor1_b',
            'subsektor2_a',
            'subsektor2_b',
            'subsektor3_a',
            'subsektor3_b',
            'subsektor4_a',
            'subsektor4_b',
            'subsektor4_c',
            'subsektor5_a',
            'subsektor5_b',
            'subsektor5_c',
            'subsektor6_a',
            'subsektor6_b',
            'subsektor6_c',
            'subsektor7_a',
            'jumlah_art',
            'jumlah_unit_usaha',
        )
            ->where('ruta.kode_kab', "LIKE", "%" . $request->kode_kab . "%")
            ->where('ruta.kode_kec', "LIKE", "%" . $request->kode_kec . "%")
            ->where('ruta.kode_desa', "LIKE", "%" . $request->kode_desa . "%")
            ->where('ruta.id_sls', "LIKE", "%" . $id_sls . "%")
            ->where('ruta.id_sub_sls', "LIKE", "%" . $id_sub_sls . "%")
            ->get();
        foreach ($data as $ruta) {
            $rowData = [
                $ruta->kode_prov,
                $ruta->kode_kab,
                $ruta->kode_kec,
                $ruta->kode_desa,
                $ruta->id_sls,
                $ruta->id_sub_sls,
                $ruta->nurt,
                $ruta->kepala_ruta,
                $ruta->sls->kode_pcl,
                $ruta->sls->kode_pml,
                $ruta->sls->kode_koseka,
                $ruta->start_time,
                $ruta->end_time,
                $ruta->start_latitude,
                $ruta->end_latitude,
                $ruta->start_longitude,
                $ruta->end_longitude,
                $ruta->subsektor1_a,
                $ruta->subsektor1_b,
                $ruta->subsektor2_a,
                $ruta->subsektor2_b,
                $ruta->subsektor3_a,
                $ruta->subsektor3_b,
                $ruta->subsektor4_a,
                $ruta->subsektor4_b,
                $ruta->subsektor4_c,
                $ruta->subsektor5_a,
                $ruta->subsektor5_b,
                $ruta->subsektor5_c,
                $ruta->subsektor6_a,
                $ruta->subsektor6_b,
                $ruta->subsektor6_c,
                $ruta->subsektor7_a,
                $ruta->jumlah_art,
                $ruta->jumlah_unit_usaha,
            ];
            fputcsv($file, $rowData);
        }
        fclose($file);
        return response()->download(storage_path('app/export.csv'), 'export.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function export_progress(Request $request)
    {
        return excel::download(new ProgressExport($request), 'progress_16.xlsx');
    }

    public function export_dokumen(Request $request)
    {
        return excel::download(new DokumenExport($request), 'dokumen_16.xlsx');
    }

    public function export_target(Request $request)
    {
        return excel::download(new TargetExport($request), 'target_16.xlsx');
    }

    public function export_dashboard_waktu(Request $request)
    {
        return excel::download(new DashboardWaktuExport($request), 'waktu_16.xlsx');
    }
    public function export_dashboard_lokasi(Request $request)
    {
        // dd($request->all());
        return excel::download(new DashboardLokasiExport($request), 'lokasi_16.xlsx');
    }

    public function export_dashboard_pendampingan(Request $request)
    {
        return excel::download(new DashboardPendampinganExport($request), 'pendampingan_16.xlsx');
    }

    public function export_user(Request $request)
    {
        return excel::download(new UserExport($request), 'user_16.xlsx');
    }

    public function export_sls_perubahan(Request $request)
    {
        return excel::download(new SlsPerubahanExport($request), 'sls_16.xlsx');
    }
}

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
use App\Exports\TargetExport;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
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
        return excel::download(new RutaExport($request), 'ruta_16.xlsx');
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
}

<?php

namespace App\Http\Controllers\Api;

use App\Exports\AlokasiExport;
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
     *     summary="Export Alokasi",
     *     description="Export Daftar SLS besertar usernya (untuk import alokasi petugas)",
     *     operationId="export_alokasi",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File uploaded successfully"
     *     )
     * )
     */
    public function export_alokasi(Request $request)
    {
        return Excel::download(new AlokasiExport($request), 'alokasi.xlsx');
    }
}

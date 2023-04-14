<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Sls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    /**
     * @OA\Get(
     *     path="/api/progress_per_kab",
     *     tags={"Dashboard"},
     *     summary="Get Progress",
     *     description="Progress penyelesaian SLS perkabupatenkota",
     *     operationId="dashboard",
     *     @OA\Response(
     *         response="default",
     *         description="return array model kategori"
     *     )
     * )
     */
    public function progress_per_kab()
    {
        $data = Sls::select('kode_kab',  DB::raw('SUM(status_selesai_pcl) as selesai'),  DB::raw('COUNT(*) as total_sls'))
            ->groupby('kode_kab')
            ->orderBy('kode_kab', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $data]);
    }
}

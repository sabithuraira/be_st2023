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
    public function progress(Request $request)
    {
        // $data = [];
        // dd($request->all());
        if ($request->sls_filter) {
            $data  = Ruta::select('kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls', 'start_time', 'end_time', 'start_latitude', 'end_latitude', 'start_longitude', 'end_longitude')
                ->where('kode_kab', $request->kab_filter)
                ->where('kode_kec', $request->kec_filter)
                ->where('kode_desa', $request->desa_filter)
                ->where('id_sls', $request->sls_filter)
                ->orderBy('kode_kab', 'asc')
                ->get();
        } else if ($request->desa_filter) {
            $data = Sls::select('id_sls',  DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah'))

                ->where('kode_kab', $request->kab_filter)
                ->where('kode_kec', $request->kec_filter)
                ->where('kode_desa', $request->desa_filter)
                ->groupby('id_sls')
                ->orderBy('id_sls', 'asc')
                ->get();
        } else if ($request->kec_filter) {
            $data = Sls::select('id_desa', 'nama_desa',  DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah'))
                ->leftJoin('desas', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'desas.id_kab')
                        ->on('master_sls.kode_kec', '=', 'desas.id_kec')
                        ->on('master_sls.kode_desa', '=', 'desas.id_desa');
                })
                ->where('kode_kab', $request->kab_filter)
                ->where('kode_kec', $request->kec_filter)
                ->groupby('id_desa', 'nama_desa')
                ->orderBy('kode_desa', 'asc')
                ->get();
        } else if ($request->kab_filter) {
            $data = Sls::select('kode_kec', 'nama_kec',  DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah'))
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->where('kode_kab', $request->kab_filter)
                ->groupby('kode_kec', 'nama_kec')
                ->orderBy('kode_kec', 'asc')
                ->get();
        } else {
            $data = Sls::select('kode_kab', 'nama_kab', 'alias',  DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah'))
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->groupby('kode_kab', 'nama_kab', 'alias')
                ->orderBy('kode_kab', 'asc')
                ->get();
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }
}

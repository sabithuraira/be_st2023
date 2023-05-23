<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desas;
use App\Models\Kabs;
use App\Models\Kecs;
use App\Models\Ruta;
use App\Models\Sls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/progress",
     *     tags={"Dashboard"},
     *     summary="Get Progress selesai cacah",
     *     description="progress selesai persls dengan filter wilayah sampai level sls(list ruta)",
     *     operationId="progress",
     *     @OA\Parameter(
     *          name="Bearer Token",
     *          description="",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="kab_filter",
     *          description="filter kode kabupaten",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="kec_filter",
     *          description="filter kode kecamatan",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     * *     @OA\Parameter(
     *          name="desa_filter",
     *          description="filter kode desa",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     * *     @OA\Parameter(
     *          name="sls_filter",
     *          description="filter kode sls",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="return array wilayah"
     *     )
     * )
     **/

    public function progress(Request $request)
    {

        $label_kab = "";
        $label_kec = "";
        $label_desa = "";
        $label_sls = "";

        $label_kab = Kabs::where('id_kab', $request->kab_filter)
            ->pluck('nama_kab')->first();
        $label_kec = Kecs::where('id_kab', $request->kab_filter)
            ->where('id_kec', $request->kec_filter)
            ->pluck('nama_kec')->first();
        $label_desa = Desas::where('id_kab', $request->kab_filter)
            ->where('id_kec', $request->kec_filter)
            ->where('id_desa', $request->desa_filter)
            ->pluck('nama_desa')->first();
        $label_sls = Sls::where('kode_kab', $request->kab_filter)
            ->where('kode_kec', $request->kec_filter)
            ->where('kode_desa', $request->desa_filter)
            ->where('id_sls', $request->sls_filter)
            ->pluck('nama_sls')->first();

        if ($request->sls_filter) {
            $data  = Ruta::select('ruta.kode_kab', 'nama_kab', 'ruta.kode_kec', 'nama_kec', 'ruta.kode_desa', 'nama_desa', 'ruta.id_sls', 'nama_sls', 'ruta.id_sub_sls', 'nurt', 'subsektor1_a', 'kepala_ruta', 'start_time', 'end_time', 'start_latitude', 'end_latitude', 'start_longitude', 'end_longitude')
                ->leftJoin('master_sls', function ($join) {
                    $join->on('ruta.kode_kab', '=', 'master_sls.kode_kab')
                        ->on('ruta.kode_kec', '=', 'master_sls.kode_kec')
                        ->on('ruta.kode_desa', '=', 'master_sls.kode_desa')
                        ->on('ruta.id_sls', '=', 'master_sls.id_sls')
                        ->on('ruta.id_sub_sls', '=', 'master_sls.id_sub_sls');
                })
                ->leftJoin('desas', function ($join) {
                    $join->on('ruta.kode_kab', '=', 'desas.id_kab')
                        ->on('ruta.kode_kec', '=', 'desas.id_kec')
                        ->on('ruta.kode_desa', '=', 'desas.id_desa');
                })
                ->leftJoin('kecs', function ($join) {
                    $join->on('ruta.kode_kab', '=', 'kecs.id_kab')
                        ->on('ruta.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('ruta.kode_kab', '=', 'kabs.id_kab');
                })
                ->where('ruta.kode_kab', $request->kab_filter)
                ->where('ruta.kode_kec', $request->kec_filter)
                ->where('ruta.kode_desa', $request->desa_filter)
                ->where('ruta.id_sls', $request->sls_filter)
                ->orderBy('nurt', 'asc')
                ->get();
        } else if ($request->desa_filter) {
            $data = Sls::select(
                'master_sls.kode_kab',
                'nama_kab',
                'master_sls.kode_kec',
                'nama_kec',
                'master_sls.kode_desa',
                'nama_desa',
                'master_sls.id_sls',
                'master_sls.id_sls as kode_wilayah',
                'nama_sls as nama_wilayah',
                DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah, COUNT(ruta.id) as ruta_selesai, SUM(master_sls.jml_keluarga_tani) as perkiraan_ruta')
            )
                ->leftJoin('desas', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'desas.id_kab')
                        ->on('master_sls.kode_kec', '=', 'desas.id_kec')
                        ->on('master_sls.kode_desa', '=', 'desas.id_desa');
                })
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->leftjoin('ruta', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->where('master_sls.kode_kab', $request->kab_filter)
                ->where('master_sls.kode_kec', $request->kec_filter)
                ->where('master_sls.kode_desa', $request->desa_filter)
                ->groupby('kode_kab', 'nama_kab', 'kode_kec', 'nama_kec', 'kode_desa', 'nama_desa', 'id_sls', 'nama_sls')
                ->orderBy('id_sls', 'asc')
                ->get();
        } else if ($request->kec_filter) {
            $data = Sls::select(
                'master_sls.kode_kab',
                'nama_kab',
                'master_sls.kode_kec',
                'nama_kec',
                'master_sls.kode_desa',
                'master_sls.kode_desa as kode_wilayah',
                'nama_desa as nama_wilayah',
                DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah,  COUNT(ruta.id) as ruta_selesai, SUM(master_sls.jml_keluarga_tani) as perkiraan_ruta')
            )
                ->leftJoin('desas', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'desas.id_kab')
                        ->on('master_sls.kode_kec', '=', 'desas.id_kec')
                        ->on('master_sls.kode_desa', '=', 'desas.id_desa');
                })
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->leftjoin('ruta', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->where('master_sls.kode_kab', $request->kab_filter)
                ->where('master_sls.kode_kec', $request->kec_filter)
                ->groupby('kode_kab', 'nama_kab', 'kode_kec', 'nama_kec', 'kode_desa', 'nama_desa')
                ->orderBy('kode_desa', 'asc')
                ->get();
        } else if ($request->kab_filter) {
            $data = Sls::select(
                'master_sls.kode_kab',
                'nama_kab',
                'master_sls.kode_kec',
                'master_sls.kode_kec as kode_wilayah',
                'nama_kec as nama_wilayah',
                DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah,  COUNT(ruta.id) as ruta_selesai, SUM(master_sls.jml_keluarga_tani) as perkiraan_ruta')
            )
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->leftjoin('ruta', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->where('master_sls.kode_kab', $request->kab_filter)
                ->groupby('kode_kab', 'nama_kab', 'kode_kec', 'nama_kec')
                ->orderBy('kode_kec', 'asc')
                ->get();
        } else {
            $data = Sls::select(
                'master_sls.kode_kab',
                'master_sls.kode_kab as kode_wilayah',
                'nama_kab as nama_wilayah',
                'alias',
                DB::raw('SUM(status_selesai_pcl) as selesai, COUNT(*) as jumlah, COUNT(ruta.id) as ruta_selesai, SUM(master_sls.jml_keluarga_tani) as perkiraan_ruta')
            )
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->leftjoin('ruta', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->groupby('kode_kab', 'nama_kab', 'alias')
                ->orderBy('kode_kab', 'asc')
                ->get();
        }

        return response()->json(['status' => 'success', 'data' => $data, 'label_kab' => $label_kab, 'label_kec' => $label_kec, 'label_desa' => $label_desa, 'label_sls' => $label_sls]);
    }

    // /**
    //  * @OA\Get(
    //  *     path="/api/progress_kk",
    //  *     tags={"Dashboard"},
    //  *     summary="Get Progress jumlah kk berdasarkan sektor dan keseluruhan",
    //  *     description="progress jumlah kk yang telah diinput dengan filter wilayah sampai ke level desa",
    //  *     operationId="progress_kk",
    //  *     @OA\Parameter(
    //  *          name="Bearer Token",
    //  *          description="",
    //  *          required=true,
    //  *          in="header",
    //  *          @OA\Schema(
    //  *              type="string"
    //  *          )
    //  *     ),
    //  *     @OA\Parameter(
    //  *          name="kab_filter",
    //  *          description="filter kode kabupaten",
    //  *          required=false,
    //  *          in="path",
    //  *          @OA\Schema(
    //  *              type="string"
    //  *          )
    //  *     ),
    //  *     @OA\Parameter(
    //  *          name="kec_filter",
    //  *          description="filter kode kecamatan",
    //  *          required=false,
    //  *          in="path",
    //  *          @OA\Schema(
    //  *              type="string"
    //  *          )
    //  *     ),
    //  * *     @OA\Parameter(
    //  *          name="desa_filter",
    //  *          description="filter kode desa",
    //  *          required=false,
    //  *          in="path",
    //  *          @OA\Schema(
    //  *              type="string"
    //  *          )
    //  *     ),
    //  *     @OA\Response(
    //  *         response="default",
    //  *         description="return array wilayah & progress"
    //  *     )
    //  * )
    //  */

    public function progress_kk(Request $request)
    {
        $sub = Ruta::where('kode_kab', 'LIKE', '%' . $request->kab_filter . '%')
            ->where('kode_kec', 'LIKE', '%' . $request->kec_filter . '%')
            ->where('kode_desa', 'LIKE', '%' . $request->desa_filter . '%')
            ->select(
                'kode_kab',
                'kode_kec',
                'kode_desa',
                'id_sls',
                'id_sub_sls',
                DB::raw('SUM(CASE WHEN sektor = 1 THEN 1 ELSE 0 END) AS sektor1'),
                DB::raw('SUM(CASE WHEN sektor = 2 THEN 1 ELSE 0 END) AS sektor2'),
                DB::raw('SUM(CASE WHEN sektor = 3 THEN 1 ELSE 0 END) AS sektor3'),
                DB::raw('SUM(CASE WHEN sektor = 4 THEN 1 ELSE 0 END) AS sektor4'),
                DB::raw('SUM(CASE WHEN sektor = 5 THEN 1 ELSE 0 END) AS sektor5'),
                DB::raw('SUM(CASE WHEN sektor = 6 THEN 1 ELSE 0 END) AS sektor6'),
            )
            ->groupby(
                'kode_kab',
                'kode_kec',
                'kode_desa',
                'id_sls',
                'id_sub_sls',
            );

        if ($request->desa_filter) {
            $data = DB::table(DB::raw("({$sub->toSql()}) as ruta"))
                ->select(
                    'master_sls.kode_kab',
                    'master_sls.kode_kec',
                    'master_sls.kode_desa',
                    'master_sls.id_sls as kode_wilayah',
                    'master_sls.nama_sls as nama_wilayah',
                    DB::raw('SUM(ruta.sektor1) as st_sektor1'),
                    DB::raw('SUM(ruta.sektor2) as st_sektor2'),
                    DB::raw('SUM(ruta.sektor3) as st_sektor3'),
                    DB::raw('SUM(ruta.sektor4) as st_sektor4'),
                    DB::raw('SUM(ruta.sektor5) as st_sektor5'),
                    DB::raw('SUM(ruta.sektor6) as st_sektor6'),
                    DB::raw('SUM(master_sls.sektor1) as reg_sektor1'),
                    DB::raw('SUM(master_sls.sektor2) as reg_sektor2'),
                    DB::raw('SUM(master_sls.sektor3) as reg_sektor3'),
                    DB::raw('SUM(master_sls.sektor4) as reg_sektor4'),
                    DB::raw('SUM(master_sls.sektor5) as reg_sektor5'),
                    DB::raw('SUM(master_sls.sektor6) as reg_sektor6'),
                    DB::raw('SUM(master_sls.jml_art_tani) as reg_art_tani'),
                    DB::raw('SUM(master_sls.jml_keluarga_tani) as reg_kk_tani'),
                )
                ->mergeBindings($sub->getQuery())
                ->join('master_sls', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })

                ->where('master_sls.kode_kab', $request->kab_filter)
                ->where('master_sls.kode_kec', $request->kec_filter)
                ->where('master_sls.kode_desa', $request->desa_filter)
                ->groupby(
                    'master_sls.kode_kab',
                    'master_sls.kode_kec',
                    'master_sls.kode_desa',
                    'master_sls.id_sls',
                    'master_sls.nama_sls',
                )
                ->get();
        } else if ($request->kec_filter) {
            $data = DB::table(DB::raw("({$sub->toSql()}) as ruta"))
                ->select(
                    'master_sls.kode_kab',
                    'master_sls.kode_kec',
                    'master_sls.kode_desa as kode_wilayah',
                    'nama_desa as nama_wilayah',
                    DB::raw('SUM(ruta.sektor1) as st_sektor1'),
                    DB::raw('SUM(ruta.sektor2) as st_sektor2'),
                    DB::raw('SUM(ruta.sektor3) as st_sektor3'),
                    DB::raw('SUM(ruta.sektor4) as st_sektor4'),
                    DB::raw('SUM(ruta.sektor5) as st_sektor5'),
                    DB::raw('SUM(ruta.sektor6) as st_sektor6'),
                    DB::raw('SUM(master_sls.sektor1) as reg_sektor1'),
                    DB::raw('SUM(master_sls.sektor2) as reg_sektor2'),
                    DB::raw('SUM(master_sls.sektor3) as reg_sektor3'),
                    DB::raw('SUM(master_sls.sektor4) as reg_sektor4'),
                    DB::raw('SUM(master_sls.sektor5) as reg_sektor5'),
                    DB::raw('SUM(master_sls.sektor6) as reg_sektor6'),
                    DB::raw('SUM(master_sls.jml_art_tani) as reg_art_tani'),
                    DB::raw('SUM(master_sls.jml_keluarga_tani) as reg_kk_tani'),
                )
                ->mergeBindings($sub->getQuery())
                ->join('master_sls', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->where('master_sls.kode_kab', $request->kab_filter)
                ->where('master_sls.kode_kec', $request->kec_filter)
                ->leftJoin('desas', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'desas.id_kab')
                        ->on('master_sls.kode_kec', '=', 'desas.id_kec')
                        ->on('master_sls.kode_desa', '=', 'desas.id_desa');
                })
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->groupby(
                    'master_sls.kode_kab',
                    'master_sls.kode_kec',
                    'master_sls.kode_desa',
                    'nama_desa',
                )
                ->get();
        } else if ($request->kab_filter) {
            $data = DB::table(DB::raw("({$sub->toSql()}) as ruta"))
                ->select(
                    'master_sls.kode_kab',
                    'master_sls.kode_kec as kode_wilayah',
                    'nama_kec as nama_wilayah',
                    DB::raw('SUM(ruta.sektor1) as st_sektor1'),
                    DB::raw('SUM(ruta.sektor2) as st_sektor2'),
                    DB::raw('SUM(ruta.sektor3) as st_sektor3'),
                    DB::raw('SUM(ruta.sektor4) as st_sektor4'),
                    DB::raw('SUM(ruta.sektor5) as st_sektor5'),
                    DB::raw('SUM(ruta.sektor6) as st_sektor6'),
                    DB::raw('SUM(master_sls.sektor1) as reg_sektor1'),
                    DB::raw('SUM(master_sls.sektor2) as reg_sektor2'),
                    DB::raw('SUM(master_sls.sektor3) as reg_sektor3'),
                    DB::raw('SUM(master_sls.sektor4) as reg_sektor4'),
                    DB::raw('SUM(master_sls.sektor5) as reg_sektor5'),
                    DB::raw('SUM(master_sls.sektor6) as reg_sektor6'),
                    DB::raw('SUM(master_sls.jml_art_tani) as reg_art_tani'),
                    DB::raw('SUM(master_sls.jml_keluarga_tani) as reg_kk_tani'),
                )
                ->mergeBindings($sub->getQuery())
                ->join('master_sls', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->leftJoin('kecs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                        ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->where('master_sls.kode_kab', $request->kab_filter)
                ->groupby(
                    'master_sls.kode_kab',
                    'master_sls.kode_kec',
                    'nama_kec'
                )
                ->get();
        } else {
            $data = DB::table(DB::raw("({$sub->toSql()}) as ruta"))
                ->select(
                    'master_sls.kode_kab as kode_wilayah',
                    'nama_kab as nama_wilayah',
                    DB::raw('SUM(ruta.sektor1) as st_sektor1'),
                    DB::raw('SUM(ruta.sektor2) as st_sektor2'),
                    DB::raw('SUM(ruta.sektor3) as st_sektor3'),
                    DB::raw('SUM(ruta.sektor4) as st_sektor4'),
                    DB::raw('SUM(ruta.sektor5) as st_sektor5'),
                    DB::raw('SUM(ruta.sektor6) as st_sektor6'),
                    DB::raw('SUM(master_sls.sektor1) as reg_sektor1'),
                    DB::raw('SUM(master_sls.sektor2) as reg_sektor2'),
                    DB::raw('SUM(master_sls.sektor3) as reg_sektor3'),
                    DB::raw('SUM(master_sls.sektor4) as reg_sektor4'),
                    DB::raw('SUM(master_sls.sektor5) as reg_sektor5'),
                    DB::raw('SUM(master_sls.sektor6) as reg_sektor6'),
                    DB::raw('SUM(master_sls.jml_art_tani) as reg_art_tani'),
                    DB::raw('SUM(master_sls.jml_keluarga_tani) as reg_kk_tani'),
                )
                ->mergeBindings($sub->getQuery())
                ->join('master_sls', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                        ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                        ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                        ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                        ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                })
                ->leftJoin('kabs', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                })
                ->groupby(
                    'master_sls.kode_kab',
                    'kabs.nama_kab',
                )
                ->get();
        }
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/progress_dokumen",
     *     tags={"Dashboard"},
     *     summary="Get Progress dokumen",
     *     description="progress dokumen persls dengan filter wilayah",
     *     operationId="progress_dokumen",
     *     @OA\Parameter(
     *          name="Bearer Token",
     *          description="",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="kab_filter",
     *          description="filter kode kabupaten",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="kec_filter",
     *          description="filter kode kecamatan",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     * *     @OA\Parameter(
     *          name="desa_filter",
     *          description="filter kode desa",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     * *     @OA\Parameter(
     *          name="sls_filter",
     *          description="filter kode sls",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="return array wilayah"
     *     )
     * )
     */
    public function progress_dokumen(Request $request)
    {

        if ($request->desa_filter) {
            $data = Sls::select(
                'master_sls.kode_kab',
                'master_sls.kode_kec',
                'master_sls.kode_desa',
                'master_sls.id_sls',
                'master_sls.id_sub_sls',
                'master_sls.id_sub_sls as kode_wilayah',
                'nama_sls as nama_wilayah',
                'kode_pcl',
                'kode_pml',
                'kode_koseka',
                DB::raw(
                    'COUNT(nurt) as dok_pcl'
                ),
                'jml_dok_ke_pml as dok_pml',
                'jml_dok_ke_koseka as dok_koseka',
                'jml_nr',
                'jml_dok_ke_bps as dok_bps'
            )->leftJoin(
                'ruta',
                function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab');
                    $join->on('master_sls.kode_kec', '=', 'ruta.kode_kec');
                    $join->on('master_sls.kode_desa', '=', 'ruta.kode_desa');
                    $join->on('master_sls.id_sls', '=', 'ruta.id_sls');
                    $join->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                }
            )
                ->where('master_sls.kode_kab', $request->kab_filter)
                ->where('master_sls.kode_kec', $request->kec_filter)
                ->where('master_sls.kode_desa', $request->desa_filter)
                ->groupby(
                    'master_sls.kode_kab',
                    'master_sls.kode_kec',
                    'master_sls.kode_desa',
                    'master_sls.id_sls',
                    'master_sls.id_sub_sls',
                    'master_sls.nama_sls',
                    'kode_pcl',
                    'kode_pml',
                    'kode_koseka',
                    'jml_dok_ke_pml',
                    'jml_dok_ke_koseka',
                    'jml_nr',
                    'jml_dok_ke_bps',
                )
                ->orderBy('id_sls', 'asc')
                ->orderBy('id_sub_sls', 'asc')
                ->get();
        } else if ($request->kec_filter) {
            $data = Sls::select(
                'master_sls.kode_kab',
                'master_sls.kode_kec',
                'master_sls.kode_desa',
                'master_sls.kode_desa as kode_wilayah',
                'nama_desa as nama_wilayah',
                DB::raw(
                    'COUNT(ruta.nurt) as dok_pcl,
                    SUM(jml_dok_ke_pml) as dok_pml,
                    SUM(jml_dok_ke_koseka) as dok_koseka,
                    SUM(jml_nr) as jml_nr,
                    SUM(jml_dok_ke_bps) as dok_bps'
                )
            )->leftJoin(
                'ruta',
                function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab');
                    $join->on('master_sls.kode_kec', '=', 'ruta.kode_kec');
                    $join->on('master_sls.kode_desa', '=', 'ruta.kode_desa');
                    $join->on('master_sls.id_sls', '=', 'ruta.id_sls');
                    $join->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                }
            )
                ->leftJoin('desas', function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'desas.id_kab')
                        ->on('master_sls.kode_kec', '=', 'desas.id_kec')
                        ->on('master_sls.kode_desa', '=', 'desas.id_desa');
                })
                ->where('master_sls.kode_kab', $request->kab_filter)
                ->where('master_sls.kode_kec', $request->kec_filter)
                ->groupby('master_sls.kode_kab', 'master_sls.kode_kec', 'master_sls.kode_desa', 'master_sls.kode_desa', 'nama_desa')
                ->orderBy('master_sls.kode_desa', 'asc')
                ->get();
        } else if ($request->kab_filter) {
            $data = Sls::select(
                'master_sls.kode_kab',
                'master_sls.kode_kec',
                'master_sls.kode_kec as kode_wilayah',
                'nama_kec as nama_wilayah',
                DB::raw(
                    'COUNT(ruta.nurt) as dok_pcl,
                    SUM(jml_dok_ke_pml) as dok_pml,
                    SUM(jml_dok_ke_koseka) as dok_koseka,
                    SUM(jml_nr) as jml_nr,
                    SUM(jml_dok_ke_bps) as dok_bps'
                )
            )->leftJoin(
                'ruta',
                function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab');
                    $join->on('master_sls.kode_kec', '=', 'ruta.kode_kec');
                    $join->on('master_sls.kode_desa', '=', 'ruta.kode_desa');
                    $join->on('master_sls.id_sls', '=', 'ruta.id_sls');
                    $join->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                }
            )->leftJoin('kecs', function ($join) {
                $join->on('master_sls.kode_kab', '=', 'kecs.id_kab')
                    ->on('master_sls.kode_kec', '=', 'kecs.id_kec');
            })
                ->where('master_sls.kode_kab', $request->kab_filter)
                ->groupby('master_sls.kode_kab', 'master_sls.kode_kec', 'nama_kec')
                ->orderBy('master_sls.kode_kec', 'asc')
                ->get();
        } else {
            $data = Sls::select(
                'master_sls.kode_kab',
                'master_sls.kode_kab as kode_wilayah',
                'nama_kab as nama_wilayah',
                'alias',
                DB::raw(
                    // SUM(status_selesai_pcl) as dok_pcl,
                    '
                    COUNT(ruta.nurt) as dok_pcl,
                    SUM(jml_dok_ke_pml) as dok_pml,
                    SUM(jml_dok_ke_koseka) as dok_koseka,
                    SUM(jml_nr) as jml_nr,
                    SUM(jml_dok_ke_bps) as dok_bps'
                ),
            )->leftjoin(
                'ruta',
                function ($join) {
                    $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab');
                    $join->on('master_sls.kode_kec', '=', 'ruta.kode_kec');
                    $join->on('master_sls.kode_desa', '=', 'ruta.kode_desa');
                    $join->on('master_sls.id_sls', '=', 'ruta.id_sls');
                    $join->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
                }
            )
                ->leftJoin(
                    'kabs',
                    function ($join) {
                        $join->on('master_sls.kode_kab', '=', 'kabs.id_kab');
                    }
                )
                ->groupby('kode_kab', 'nama_kab', 'alias')
                ->orderBy('kode_kab', 'asc')
                ->get();
        }
        return response()->json(['status' => 'success', 'data' => $data]);
    }


    public function dashboard_waktu(Request $request)
    {
        $per_page = 20;
        $datas = [];
        $condition = [];
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $request->kec_filter];
        if (isset($request->desa_filter) && strlen($request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $request->desa_filter];

        //PAGINATION
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;

        //KEYWORD CONDITION
        $datas = [];
        $datas =  DB::table('dashboard_waktu')
            ->select('kode_kab', 'pcl', 'pml', 'koseka', DB::raw('AVG(TIME_TO_SEC(time_difference)) / 60 AS rata_rata_waktu_menit, COUNT(*) as jml_ruta'))
            ->groupBy('kode_kab', 'pcl', 'pml', 'koseka')
            ->where($condition)
            ->orderBy('kode_kab')
            ->orderBy('kode_kec')
            ->orderBy('kode_desa')
            ->orderBy('id_sls')
            ->orderBy('id_sub_sls')
            ->paginate($per_page);
        $datas->withPath('dashboard_waktu');
        $datas->appends($request->all());

        return response()->json(['status' => 'success', 'datas' => $datas]);
    }

    public function dashboard_lokasi(Request $request)
    {
        $per_page = 20;
        $datas = [];
        $condition = [];
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $request->kec_filter];
        if (isset($request->desa_filter) && strlen($request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $request->desa_filter];

        //PAGINATION
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;

        //KEYWORD CONDITION
        $datas = [];
        $datas =  DB::table('dashboard_lokasi')
            ->select('kode_kab', 'pcl', 'pml', 'koseka', DB::raw('AVG(distance) * 1000 as rata_rata_jarak, COUNT(*) as jml_ruta'))
            ->groupBy('kode_kab', 'pcl', 'pml', 'koseka')
            ->where($condition)
            ->orderBy('kode_kab')
            ->orderBy('kode_kec')
            ->orderBy('kode_desa')
            ->orderBy('id_sls')
            ->orderBy('id_sub_sls')
            ->paginate($per_page);
        $datas->withPath('dashboard_lokasi');
        $datas->appends($request->all());

        return response()->json(['status' => 'success', 'datas' => $datas]);
    }

    public function dashboard_target(Request $request)
    {
        $per_page = 20;
        $datas = [];
        $condition = [];
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $request->kec_filter];
        if (isset($request->desa_filter) && strlen($request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $request->desa_filter];

        //PAGINATION
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;

        //KEYWORD CONDITION
        $datas = [];
        $datas = DB::view('dashboard_target')
            ->where($condition)
            ->orderBy('kode_kab')
            ->orderBy('kode_kec')
            ->orderBy('kode_desa')
            ->orderBy('id_sls')
            ->orderBy('id_sub_sls')
            ->paginate($per_page);
        $datas->withPath('dashboard_target');
        $datas->appends($request->all());

        return response()->json(['status' => 'success', 'datas' => $datas]);
    }
}

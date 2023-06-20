<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desas;
use App\Models\Kabs;
use App\Models\Kecs;
use App\Models\Ruta;
use App\Models\Sls;
use App\Models\User;
use Carbon\Carbon;
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
            $id_sls = substr($request->sls_filter, 0, 4);
            $id_sub_sls = substr($request->sls_filter, 4, 2);
            $data = Ruta::with('kab')
                ->with('kec')
                ->with('desa')
                ->with('sls:kode_kab,kode_kec,kode_desa,kode_desa,id_sls,id_sub_sls,nama_sls')
                ->where('ruta.kode_kab', $request->kab_filter)
                ->where('ruta.kode_kec', $request->kec_filter)
                ->where('ruta.kode_desa', $request->desa_filter)
                ->where('ruta.id_sls', $id_sls)
                ->where('ruta.id_sub_sls', $id_sub_sls)
                ->get();
        } else if ($request->desa_filter) {
            $data = Sls::select(
                'kode_kab',
                'kode_kec',
                'kode_desa',
                DB::raw("CONCAT(id_sls, id_sub_sls) AS id_sls, CONCAT(id_sls, id_sub_sls) AS kode_wilayah, nama_sls as nama_wilayah, '1' as jumlah"),
                'status_selesai_pcl as selesai',
                'jml_keluarga_tani as  perkiraan_ruta',
                'ruta_prelist as prelist_ruta',
                'prelist_ruta_tani as prelist_ruta_tani'
            )
                ->withCount('ruta as ruta_selesai')
                ->where('kode_kab', $request->kab_filter)
                ->where('kode_kec', $request->kec_filter)
                ->where('kode_desa', $request->desa_filter)
                ->get();
        } else if ($request->kec_filter) {
            $data = Desas::select(
                'id_kab as kode_kab',
                'id_kec as kode_kec',
                'id_desa as kode_desa',
                'id_desa as kode_wilayah',
                'nama_desa as nama_wilayah'
            )->withCount('sls as jumlah')
                ->withSum('sls as selesai', 'status_selesai_pcl')
                ->withSum('sls as perkiraan_ruta', 'jml_keluarga_tani')
                ->withSum('sls as prelist_ruta', 'ruta_prelist')
                ->withSum('sls as prelist_ruta_tani', 'prelist_ruta_tani')
                ->withCount('ruta as ruta_selesai')
                ->where('id_kab', $request->kab_filter)
                ->where('id_kec', $request->kec_filter)
                ->get();
        } else if ($request->kab_filter) {
            $data = Kecs::select(
                'id_kab as kode_kab',
                'id_kec as kode_kec',
                'id_kec as kode_wilayah',
                'nama_kec as nama_wilayah'
            )->withCount('sls as jumlah')
                ->withSum('sls as selesai', 'status_selesai_pcl')
                ->withSum('sls as perkiraan_ruta', 'jml_keluarga_tani')
                ->withSum('sls as prelist_ruta', 'ruta_prelist')
                ->withSum('sls as prelist_ruta_tani', 'prelist_ruta_tani')
                ->withCount('ruta as ruta_selesai')
                ->where('id_kab', $request->kab_filter)
                ->orderby('id_kec')
                ->get();
        } else {
            $data = Kabs::select(
                'id_kab as kode_kab',
                'id_kab as kode_wilayah',
                'nama_kab as nama_wilayah',
                'alias'
            )->withCount('sls as jumlah')
                ->withSum('sls as selesai', 'status_selesai_pcl')
                ->withSum('sls as perkiraan_ruta', 'jml_keluarga_tani')
                ->withSum('sls as prelist_ruta', 'ruta_prelist')
                ->withSum('sls as prelist_ruta_tani', 'prelist_ruta_tani')
                ->withCount('ruta as ruta_selesai')
                ->get();
        }

        return response()->json(['status' => 'success', 'data' => $data, 'label_kab' => $label_kab, 'label_kec' => $label_kec, 'label_desa' => $label_desa, 'label_sls' => $label_sls]);
    }

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
                'kode_kab',
                'kode_kec',
                'kode_desa',
                DB::raw("CONCAT(id_sls, id_sub_sls) AS id_sls,
                 CONCAT(id_sls, id_sub_sls) AS kode_wilayah,
                 nama_sls as nama_wilayah,
                 sum(jml_dok_ke_pml) as dok_pml,
                 sum(jml_dok_ke_koseka) as dok_koseka,
                 sum(jml_nr) as jml_nr,
                 sum(jml_dok_ke_bps) as dok_bps
                 "),
                'kode_pcl',
                'kode_pml',
                'kode_koseka'
            )->withCount('ruta as dok_pcl')
                ->where('kode_kab', $request->kab_filter)
                ->where('kode_kec', $request->kec_filter)
                ->where('kode_desa', $request->desa_filter)
                ->groupby(
                    'kode_kab',
                    'kode_kec',
                    'kode_desa',
                    'id_sls',
                    'id_sub_sls',
                    'nama_sls',
                    'kode_pcl',
                    'kode_pml',
                    'kode_koseka'
                )
                ->get();
        } else if ($request->kec_filter) {
            $data = Desas::select(
                'id_kab as kode_kab',
                'id_kec as kode_kec',
                'id_desa as kode_desa',
                'id_desa as kode_wilayah',
                'nama_desa as nama_wilayah'
            )->withCount('ruta as dok_pcl')
                ->withSum('sls as dok_pml', 'jml_dok_ke_pml')
                ->withSum('sls as dok_koseka', 'jml_dok_ke_koseka')
                ->withSum('sls as jml_nr', 'jml_nr')
                ->withSum('sls as dok_bps', 'jml_dok_ke_bps')
                ->where('id_kab', $request->kab_filter)
                ->where('id_kec', $request->kec_filter)
                ->get();
        } else if ($request->kab_filter) {

            $data = Kecs::select(
                'id_kab as kode_kab',
                'id_kec as kode_kec',
                'id_kec as kode_wilayah',
                'nama_kec as nama_wilayah'
            )->withCount('ruta as dok_pcl')
                ->withSum('sls as dok_pml', 'jml_dok_ke_pml')
                ->withSum('sls as dok_koseka', 'jml_dok_ke_koseka')
                ->withSum('sls as jml_nr', 'jml_nr')
                ->withSum('sls as dok_bps', 'jml_dok_ke_bps')
                ->where('id_kab', $request->kab_filter)
                ->orderby('id_kec')
                ->get();
        } else {
            $data = Kabs::select(
                'id_kab as kode_kab',
                'id_kab as kode_wilayah',
                'nama_kab as nama_wilayah',
                'alias'
            )->withCount('ruta as dok_pcl')
                ->withSum('sls as dok_pml', 'jml_dok_ke_pml')
                ->withSum('sls as dok_koseka', 'jml_dok_ke_koseka')
                ->withSum('sls as jml_nr', 'jml_nr')
                ->withSum('sls as dok_bps', 'jml_dok_ke_bps')
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
        $tanggal_awal = $request->tanggal_awal ? $request->tanggal_awal : now()->subDays(7)->format('m/d/Y');
        $tanggal_akhir = $request->tanggal_akhir ? $request->tanggal_akhir : now()->format('m/d/Y');
        $awalDate = \DateTime::createFromFormat('m/d/Y', $tanggal_awal);
        $akhirDate = \DateTime::createFromFormat('m/d/Y', $tanggal_akhir);
        //PAGINATION
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;
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
            ->paginate($per_page);

        $datas->withPath('waktu');
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
        $tanggal_awal = $request->tanggal_awal ? $request->tanggal_awal : now()->subDays(7)->format('m/d/Y');
        $tanggal_akhir = $request->tanggal_akhir ? $request->tanggal_akhir : now()->format('m/d/Y');
        $awalDate = \DateTime::createFromFormat('m/d/Y', $tanggal_awal);
        $akhirDate = \DateTime::createFromFormat('m/d/Y', $tanggal_akhir);
        $datas = User::where($condition)
            ->role(["PPL"])
            ->with(['rutas' => function ($query) use ($awalDate, $akhirDate) {
                $query
                    ->whereBetween('created_at', [
                        $awalDate->format('Y-m-d 00:00:00'),
                        $akhirDate->format('Y-m-d 23:59:59')
                    ])
                    ->select('created_by', DB::raw('AVG(ABS(start_latitude) - ABS(end_latitude)) as rata_latitude, AVG(ABS(start_longitude) - ABS(end_longitude)) as rata_longitude, COUNT(*) as jml_ruta'))
                    ->groupBy('created_by');
            }])
            ->orderBy('kode_kab', 'ASC')
            ->orderBy('name', 'ASC')
            ->paginate($per_page);
        $datas->withPath('lokasi');
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
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;
        $datas = [];
        $datas = DB::table('dashboard_target')
            ->orderBy('kode_kab')
            ->orderBy('jumlah_ruta')
            ->paginate($per_page);
        $datas->withPath('target');
        $datas->appends($request->all());
        return response()->json(['status' => 'success', 'datas' => $datas]);
    }

    public function dashboard_koseka(Request $request)
    {
        $per_page = 20;
        $condition = [];
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['users.kode_kab', '=', $request->kab_filter];
        // if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['users.kode_kec', '=', $request->kec_filter];
        if (isset($request->desa_filter) && strlen($request->desa_filter) > 0) $condition[] = ['users.kode_desa', '=', $request->desa_filter];

        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;
        $datas = [];

        $datas = User::select('users.email', 'users.kode_kab', 'users.name')
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_1_juni', ['2023-06-01'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_2_juni', ['2023-06-02'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_3_juni', ['2023-06-03'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_4_juni', ['2023-06-04'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_5_juni', ['2023-06-05'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_6_juni', ['2023-06-06'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_7_juni', ['2023-06-07'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_8_juni', ['2023-06-08'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_9_juni', ['2023-06-09'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_10_juni', ['2023-06-10'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_11_juni', ['2023-06-11'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_12_juni', ['2023-06-12'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_13_juni', ['2023-06-13'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_14_juni', ['2023-06-14'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_15_juni', ['2023-06-15'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_16_juni', ['2023-06-16'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_17_juni', ['2023-06-17'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_18_juni', ['2023-06-18'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_19_juni', ['2023-06-19'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_20_juni', ['2023-06-20'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_21_juni', ['2023-06-21'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_22_juni', ['2023-06-22'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_23_juni', ['2023-06-23'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_24_juni', ['2023-06-24'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_25_juni', ['2023-06-25'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_26_juni', ['2023-06-26'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_27_juni', ['2023-06-27'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_28_juni', ['2023-06-28'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_29_juni', ['2023-06-29'])
            ->selectRaw('SUM(CASE WHEN DATE(start_time) = ? THEN 1 ELSE 0 END) AS rt_30_juni', ['2023-06-30'])
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('master_sls', 'master_sls.kode_koseka', '=', 'users.email')
            ->leftJoin('ruta', function ($join) {
                $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab');
                $join->on('master_sls.kode_kec', '=', 'ruta.kode_kec');
                $join->on('master_sls.kode_desa', '=', 'ruta.kode_desa');
                $join->on('master_sls.id_sls', '=', 'ruta.id_sls');
                $join->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
            })
            ->where($condition)
            ->where('roles.name', 'Koseka')
            ->groupBy('users.email', 'users.kode_kab', 'users.name')
            ->get();

        return response()->json(['status' => 'success', 'datas' => $datas]);
    }

    public function dashboard_pendampingan(Request $request)
    {
        $per_page = 20;
        $condition = [];
        $keyword = $request->keyword;
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['users.kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $request->kec_filter];
        if (isset($request->desa_filter) && strlen($request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $request->desa_filter];
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;
        $datas = [];
        $datas = User::role('PPL')
            ->withCount('sls_ppl as jml_sls')
            ->with(['p_pml' => function ($query) {
                $query->groupBy('kode_pcl', 'kode_pml')
                    ->select('kode_pcl', 'kode_pml', DB::raw('COUNT(pendampingan_pml) as pendampingan_pml'));
            }])
            ->with(['p_koseka' => function ($query) {
                $query->groupBy('kode_pcl', 'kode_koseka')
                    ->select('kode_pcl', 'kode_koseka', DB::raw('COUNT(pendampingan_koseka) as pendampingan_koseka'));
            }])
            ->where($condition)
            ->orderby('kode_kab')
            ->orderby('name')
            ->paginate($per_page);
        $datas->withPath('pendampingan');
        $datas->appends($request->all());
        return response()->json(['status' => 'success', 'datas' => $datas]);
    }
}

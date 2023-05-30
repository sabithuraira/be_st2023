<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desas;
use App\Models\Kabs;
use App\Models\Kecs;
use App\Models\Sls;
use Illuminate\Http\Request;

class WIlayahController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/list_kabs",
     *     tags={"Wilayah"},
     *     summary="Get List",
     *     description="-",
     *     operationId="list_kabs",
     *     @OA\RequestBody(
     *          required=false,
     *          description="wilayah",
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description="return array model ruta"
     *     )
     * )
     */
    public function list_kabs(Request $request)
    {

        $kd_kab = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '71', '72', '73', '74'];

        if ($request->kab_filter) {
            $kd_kab = [$request->kab_filter];
        }

        if ($request->kab_filter == '03') {
            $kd_kab = ['03', '12'];
            // $data = Kabs::where('id_kab', 'LIKE', '%' . $request->kab_filter . '%')->->get();
        }

        if ($request->kab_filter == '05') {
            $kd_kab = ['05', '13'];
            // $data = Kabs::where('id_kab', 'LIKE', '%' . $request->kab_filter . '%')->->get();
        }

        $data = Kabs::whereIn('id_kab', $kd_kab)->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/list_kecs",
     *     tags={"Wilayah"},
     *     summary="Get List",
     *     description="-",
     *     operationId="list_kecs",
     *     @OA\RequestBody(
     *          required=false,
     *          description="wilayah",
     *      ),
     *     @OA\Parameter(
     *          name="kab_filter",
     *          description="kode kabupaten",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="return array model ruta"
     *     )
     * )
     */
    public function list_kecs(Request $request)
    {
        $data = Kecs::where('id_kab', $request->kab_filter)->orderBy('id_kec')->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/list_desas",
     *     tags={"Wilayah"},
     *     summary="Get List",
     *     description="-",
     *     operationId="list_desas",
     *     @OA\RequestBody(
     *          required=false,
     *          description="wilayah",
     *      ),
     *     @OA\Parameter(
     *          name="kab_filter",
     *          description="kode kabupaten",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     * @OA\Parameter(
     *          name="kec_filter",
     *          description="kode kecamatan",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="return array model ruta"
     *     )
     * )
     */
    public function list_desas(Request $request)
    {
        $data = Desas::where('id_kab', $request->kab_filter)->where('id_kec', $request->kec_filter)->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }


    /**
     * @OA\Get(
     *     path="/api/list_sls",
     *     tags={"Wilayah"},
     *     summary="Get List",
     *     description="-",
     *     operationId="list_sls",
     *     @OA\RequestBody(
     *          required=false,
     *          description="wilayah",
     *      ),
     *     @OA\Parameter(
     *          name="kab_filter",
     *          description="kode kabupaten",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="kec_filter",
     *          description="kode kecamatan",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="desa_filter",
     *          description="kode desa",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="return array model ruta"
     *     )
     * )
     */
    public function list_sls(Request $request)
    {
        $data = Sls::where('kode_kab', $request->kab_filter)->where('kode_kec', $request->kec_filter)->where('kode_desa', $request->desa_filter)->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}

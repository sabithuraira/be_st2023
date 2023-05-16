<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AlokasiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/alokasi",
     *     tags={"Alokasi"},
     *     summary="Get List",
     *     description="pencarian dg keyword akan mengarah pada nama sls",
     *     operationId="alokasi_list",
     *     @OA\RequestBody(
     *          required=false,
     *          description="filter and pagination",
     *          @OA\JsonContent(
     *              @OA\Property(property="keyword", type="string"),
     *              @OA\Property(property="kab_filter", type="string"),
     *              @OA\Property(property="kec_filter", type="string"),
     *              @OA\Property(property="desa_filter", type="string"),
     *          ),
     *      ),
     *     @OA\Parameter(
     *          name="page",
     *          description="current page to show",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="return array model kategori"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $per_page = 20;
        $datas = [];

        $condition = [];
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $request->kec_filter];
        if (isset($request->desa_filter) && strlen($request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $request->desa_filter];

        //PAGINATION
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;

        //KEYWORD CONDITION
        if (isset($request->keyword) && strlen($request->keyword) > 0) {
            $datas = Sls::where($condition)
                ->where(
                    (function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', '%' . $keyword . '%')
                            ->orWhere('tag', 'LIKE', '%' . $keyword . '%');
                    })
                )
                ->orderBy('kode_kab')
                ->orderBy('kode_kec')
                ->orderBy('kode_desa')
                ->orderBy('id_sls')
                ->orderBy('id_sub_sls')
                ->paginate($per_page);
        } else {
            $datas = Sls::where($condition)
                ->orderBy('kode_kab')
                ->orderBy('kode_kec')
                ->orderBy('kode_desa')
                ->orderBy('id_sls')
                ->orderBy('id_sub_sls')
                ->paginate($per_page);
        }

        $datas->withPath('alokasi');
        $datas->appends($request->all());

        return response()->json(['status' => 'success', 'datas' => $datas]);
    }

    /**
     * @OA\Get(
     *     path="/api/alokasi/{id}",
     *     tags={"alokasi"},
     *     summary="Detail alokasi",
     *     description="-",
     *     operationId="alokasi/show",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID Enkripsi",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="jenis return data tergantung kode wilayah yang di request"
     *     )
     * )
     */
    public function show($id)
    {
        $status = "success";

        try {
            $decryptId = Crypt::decryptString($id);
            $result = Sls::find($decryptId);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            $status = "error";
            $result = null;
        }

        return response()->json(['status' => $status, 'data' => $result]);
    }

    public function update(Request $request, $id)
    {
        try {
            $decryptId = Crypt::decryptString($id);
            $model = Sls::find($decryptId);
            $model->kode_pcl = $request->kode_pcl;
            $model->kode_pml = $request->kode_pml;
            $model->kode_koseka = $request->kode_koseka;
            $model->updated_by = auth()->user()->id;
            $model->save();
            return response()->json(['status' => 'success', 'data' => $model]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['status' => "error", 'data' => null]);
        }
    }
}

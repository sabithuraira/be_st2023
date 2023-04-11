<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sls;
use Validator;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\SlsRequest;

class SlsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/sls",
     *     tags={"sls"},
     *     summary="Get List",
     *     description="pencarian dg keyword akan mengarah pada nama sls",
     *     operationId="list",
     *     @OA\RequestBody(
     *          required=false,
     *          description="filter and pagination",
     *          @OA\JsonContent(
     *              @OA\Property(property="keyword", type="string"),
     *              @OA\Property(property="kode_prov", type="string"),
     *              @OA\Property(property="kode_kab", type="string"),
     *              @OA\Property(property="kode_kec", type="string"),
     *              @OA\Property(property="kode_desa", type="string"),
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
        if (isset($request->kode_prov) && strlen($request->kode_prov) > 0) $condition[] = ['kode_prov', '=', $request->kode_prov];
        if (isset($request->kode_kab) && strlen($request->kode_kab) > 0) $condition[] = ['kode_kab', '=', $request->kode_kab];
        if (isset($request->kode_kec) && strlen($request->kode_kec) > 0) $condition[] = ['kode_kec', '=', $request->kode_kec];
        if (isset($request->kode_desa) && strlen($request->kode_desa) > 0) $condition[] = ['kode_desa', '=', $request->kode_desa];

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
                ->orderBy('id', 'DESC')->paginate($per_page);
        } else {
            $datas = Sls::where($condition)->orderBy('id', 'DESC')->paginate($per_page);
        }

        $datas->withPath('sls');
        $datas->appends($request->all());

        return response()->json(['status' => 'success', 'datas' => $datas]);
    }

    /**
     * @OA\Post(
     *     path="/api/sls",
     *     tags={"sls"},
     *     summary="Store sls",
     *     description="-",
     *     operationId="sls/store",
     *     @OA\RequestBody(
     *          required=true,
     *          description="form sls",
     *          @OA\JsonContent(
     *              required={
     *                  "kode_prov", "kode_kab", "kode_kec", "kode_desa",
     *                  "id_sls", "id_sub_sls", "nama_sls" },
     *              @OA\Property(property="kode_prov", type="string"),
     *              @OA\Property(property="kode_kab", type="string"),
     *              @OA\Property(property="kode_kec", type="string"),
     *              @OA\Property(property="kode_desa", type="string"),
     *              @OA\Property(property="id_sls", type="string"),
     *              @OA\Property(property="id_sub_sls", type="date"),
     *              @OA\Property(property="nama_sls", type="string"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function store(SlsRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json(['status' => 'error', 'data' => $validator->errors()]);
        }

        $model = new Sls;
        $model->kode_prov = $request->kode_prov;
        $model->kode_kab = $request->kode_kab;
        $model->kode_kec = $request->kode_kec;
        $model->kode_desa = $request->kode_desa;
        $model->id_sls = $request->id_sls;
        $model->id_sub_sls = $request->id_sub_sls;
        $model->nama_sls = $request->nama_sls;

        $model->jenis_sls = 1;
        $model->status_sls = 1;

        $model->created_by = Auth::id();
        $model->updated_by = Auth::id();
        $model->save();

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/sls/{id}/show",
     *     tags={"sls"},
     *     summary="Detail sls",
     *     description="-",
     *     operationId="sls/show",
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

        return response()->json(['status' => $status, 'datas' => $result]);
    }

    /**
     * @OA\Put(
     *     path="/api/sls/{id}",
     *     tags={"sls"},
     *     summary="Update sls",
     *     description="-",
     *     operationId="sls/update",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID Enkripsi",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="form sls",
     *          @OA\JsonContent(
     *              required={
     *                  "kode_prov", "kode_kab", "kode_kec", "kode_desa",
     *                  "id_sls", "id_sub_sls", "nama_sls" },
     *              @OA\Property(property="kode_prov", type="string"),
     *              @OA\Property(property="kode_kab", type="string"),
     *              @OA\Property(property="kode_kec", type="string"),
     *              @OA\Property(property="kode_desa", type="string"),
     *              @OA\Property(property="id_sls", type="string"),
     *              @OA\Property(property="id_sub_sls", type="date"),
     *              @OA\Property(property="nama_sls", type="string"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function update(SlsRequest $request, $id)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json(['status' => 'error', 'data' => $validator->errors()]);
        }

        try {
            $decryptId = Crypt::decryptString($id);

            $model = Sls::find($decryptId);

            $model->kode_prov = $request->kode_prov;
            $model->kode_kab = $request->kode_kab;
            $model->kode_kec = $request->kode_kec;
            $model->kode_desa = $request->kode_desa;
            $model->id_sls = $request->id_sls;
            $model->id_sub_sls = $request->id_sub_sls;
            $model->nama_sls = $request->nama_sls;

            $model->jenis_sls = 1;
            $model->status_sls = 1;

            $model->updated_by = auth()->user()->id;
            $model->save();

            return response()->json(['status' => 'success', 'data' => $model]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['status' => 'error', 'data' => null]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/sls/{id}",
     *     tags={"sls"},
     *     summary="Delete sls",
     *     description="-",
     *     operationId="sls/delete",
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
     *         description=""
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $decryptId = Crypt::decryptString($id);
            $model = Sls::find($decryptId);
            $model->delete();

            return response()->json(['status' => 'success', 'data' => "Data berhasil dihapus"]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['status' => 'error', 'data' => null]);
        }
    }
}

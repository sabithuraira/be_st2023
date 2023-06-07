<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RutaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ruta",
     *     tags={"ruta"},
     *     summary="Get List",
     *     description="-",
     *     operationId="ruta/index",
     *     @OA\RequestBody(
     *          required=false,
     *          description="kode wilayah and pagination",
     *          @OA\JsonContent(
     *              @OA\Property(property="per_page", type="integer"),
     *              @OA\Property(property="kode_wilayah", type="string"),
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
     *         description="return array model ruta"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $per_page = 20;

        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;

        $condition = [];

        if (strlen($request->kode_wilayah) >= 2) $condition[] = ['kode_prov', '=', substr($request->kode_wilayah, 0, 2)];
        if (strlen($request->kode_wilayah) >= 4) $condition[] = ['kode_kab', '=', substr($request->kode_wilayah, 2, 2)];
        if (strlen($request->kode_wilayah) >= 7) $condition[] = ['kode_kec', '=', substr($request->kode_wilayah, 4, 3)];
        if (strlen($request->kode_wilayah) >= 10) $condition[] = ['kode_desa', '=', substr($request->kode_wilayah, 7, 3)];
        if (strlen($request->kode_wilayah) >= 14) $condition[] = ['id_sls', '=', substr($request->kode_wilayah, 10, 4)];
        if (strlen($request->kode_wilayah) >= 16) $condition[] = ['id_subsls', '=', substr($request->kode_wilayah, 14, 2)];

        $data = Ruta::where($condition)->orderBy('id', 'desc')->paginate($per_page);

        $data->appends($request->all());

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * @OA\Post(
     *     path="/api/ruta",
     *     tags={"ruta"},
     *     summary="Store ruta",
     *     description="-",
     *     operationId="ruta/store",
     *     @OA\RequestBody(
     *          required=true,
     *          description="form ruta",
     *          @OA\JsonContent(
     *              required={"kode_prov", "kode_kab", "kode_kec", "kode_desa", "id_sls", "id_sub_sls", "nurt"},
     *              @OA\Property(property="kode_prov", type="string"),
     *              @OA\Property(property="kode_kab", type="string"),
     *              @OA\Property(property="kode_kec", type="string"),
     *              @OA\Property(property="kode_desa", type="string"),
     *              @OA\Property(property="id_sls", type="string"),
     *              @OA\Property(property="id_sub_sls", type="string"),
     *              @OA\Property(property="nurt", type="integer"),
     *              @OA\Property(property="kepala_ruta", type="string"),
     *              @OA\Property(property="jumlah_art", type="integer"),
     *              @OA\Property(property="jumlah_unit_usaha", type="integer"),
     *              @OA\Property(property="subsektor1_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor1_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor2_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor2_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor3_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor3_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor4_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor4_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor4_c", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor5_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor5_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor5_c", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor6_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor6_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor6_c", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor7_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="jml_308_sawah", type="integer"),
     *              @OA\Property(property="jml_308_bukan_sawah", type="integer"),
     *              @OA\Property(property="jml_308_rumput_sementara", type="integer"),
     *              @OA\Property(property="jml_308_rumput_permanen", type="integer"),
     *              @OA\Property(property="jml_308_belum_tanam", type="integer"),
     *              @OA\Property(property="jml_308_ternak_bangunan_lain", type="integer"),
     *              @OA\Property(property="jml_308_kehutanan", type="integer"),
     *              @OA\Property(property="jml_308_budidaya", type="integer"),
     *              @OA\Property(property="jml_308_lahan_lainnya", type="integer"),
     *              @OA\Property(property="jml_308_tanaman_tahunan", type="integer"),
     *              @OA\Property(property="apakah_menggunakan_lahan", type="boolean"),
     *              @OA\Property(property="status_data", type="integer"),
     *              @OA\Property(property="daftar_komoditas", type="integer"),
     *              @OA\Property(property="start_time", type="datetime"),
     *              @OA\Property(property="end_time", type="datetime"),
     *              @OA\Property(property="start_latitude", type="numeric"),
     *              @OA\Property(property="end_latitude", type="numeric"),
     *              @OA\Property(property="start_longitude", type="numeric"),
     *              @OA\Property(property="end_longitude", type="numeric"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'data' => $validator->errors()]);
        }

        $data = Ruta::create([
            'kode_prov' => $request->kode_prov,
            'kode_kab' => $request->kode_kab,
            'kode_kec' => $request->kode_kec,
            'kode_desa' => $request->kode_desa,
            'id_sls' => $request->id_sls,
            'id_sub_sls' => $request->id_sub_sls,
            'nurt' => $request->nurt,
            'kepala_ruta' => $request->kepala_ruta,
            'jumlah_art' => $request->jumlah_art,
            'jumlah_unit_usaha' => $request->jumlah_unit_usaha,
            'subsektor1_a' => $request->subsektor1_a,
            'subsektor1_b' => $request->subsektor1_b,
            'subsektor2_a' => $request->subsektor2_a,
            'subsektor2_b' => $request->subsektor2_b,
            'subsektor3_a' => $request->subsektor3_a,
            'subsektor3_b' => $request->subsektor3_b,
            'subsektor4_a' => $request->subsektor4_a,
            'subsektor4_b' => $request->subsektor4_b,
            'subsektor4_c' => $request->subsektor4_c,
            'subsektor5_a' => $request->subsektor5_a,
            'subsektor5_b' => $request->subsektor5_b,
            'subsektor5_c' => $request->subsektor5_c,
            'subsektor6_a' => $request->subsektor6_a,
            'subsektor6_b' => $request->subsektor6_b,
            'subsektor6_c' => $request->subsektor6_c,
            'subsektor7_a' => $request->subsektor7_a,
            'jml_308_sawah' => $request->jml_308_sawah,
            'jml_308_bukan_sawah' => $request->jml_308_bukan_sawah,
            'jml_308_rumput_sementara' => $request->jml_308_rumput_sementara,
            'jml_308_rumput_permanen' => $request->jml_308_rumput_permanen,
            'jml_308_belum_tanam' => $request->jml_308_belum_tanam,
            'jml_308_ternak_bangunan_lain' => $request->jml_308_ternak_bangunan_lain,
            'jml_308_kehutanan' => $request->jml_308_kehutanan,
            'jml_308_budidaya' => $request->jml_308_budidaya,
            'jml_308_lahan_lainnya' => $request->jml_308_lahan_lainnya,
            'jml_308_tanaman_tahunan' => $request->jml_308_tanaman_tahunan,
            'apakah_menggunakan_lahan' => $request->apakah_menggunakan_lahan,
            'status_data' => $request->status_data,
            'daftar_komoditas' => $request->daftar_komoditas,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'start_latitude' => $request->start_latitude,
            'end_latitude' => $request->end_latitude,
            'start_longitude' => $request->start_longitude,
            'end_longitude' => $request->end_longitude,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * @OA\Post(
     *     path="/api/ruta/many",
     *     tags={"ruta"},
     *     summary="Store Many Ruta",
     *     description="You can update array here by put the enc ID on array object. If not, just set ID with '' value",
     *     operationId="ruta/many",
     *     @OA\Parameter(
     *          name="Bearer Token",
     *          description="",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="in data, just send object with property kode_prov, kode_kab, kode_kec, kode_desa, id_sls, id_sub_sls,
     *                      nurt, kepala_ruta, jumlah_art, jumlah_unit_usaha, subsektor1_a, subsektor1_b, subsektor2_a, subsektor2_b,
     *                      subsektor3_a, subsektor3_b, subsektor4_a, subsektor4_b, subsektor4_c, subsektor5_a, subsektor5_b, subsektor5_c,
     *                      subsektor6_a, subsektor6_b, subsektor6_c, subsektor7_a, jml_308_sawah, jml_308_bukan_sawah, jml_308_rumput_sementara,
     *                      jml_308_rumput_permanen, jml_308_belum_tanam, jml_308_ternak_bangunan_lain, jml_308_kehutanan, jml_308_budidaya,
     *                      jml_308_lahan_lainnya, jml_308_tanaman_tahunan, apakah_menggunakan_lahan, status_data, daftar_komoditas,
     *                      start_time, end_time, start_latitude, end_latitude, start_longitude, end_longitude",
     *          @OA\JsonContent(
     *              required={"data"},
     *              @OA\Property(property="data", type="array",
     * *      @OA\Items(
     *               type="object",
     *               description="data",
     *               @OA\Schema(type="object")
     *         ),),
     *          ),
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function store_many(Request $request)
    {
        $data_store = [];
        $data_delete = [];

        foreach ($request->data as $key => $value) {
            $value_null_to_string = [];

            foreach ($value as $k => $v) {
                if ($v) {
                    $value_null_to_string[$k] = $v;
                } else {
                    $value_null_to_string[$k] = "";
                }
            }

            $validator = $this->validator($value_null_to_string);

            if ($validator->fails()) {
                $errorString = implode(",",$validator->messages()->all());
                return response()->json(['status' => 'error', 'data' => $errorString, 'at' => $value_null_to_string]);
            }

            try {
                if ($value_null_to_string['id']) $value_null_to_string['id'] = Crypt::decryptString($value_null_to_string['id']);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return response()->json(['status' => 'error', 'data' => "Error, data ", 'at' => $value_null_to_string]);
            }

            if ($value['status_upload'] == '3') {
                $data_delete[] = $value_null_to_string['id'];
            } else {
                $data_store[] = [
                    'id' => $value_null_to_string['id'],
                    'kode_prov' => $value['kode_prov'],
                    'kode_kab' => $value['kode_kab'],
                    'kode_kec' => $value['kode_kec'],
                    'kode_desa' => $value['kode_desa'],
                    'id_sls' => $value['id_sls'],
                    'id_sub_sls' => $value['id_sub_sls'],
                    'nurt' => $value['nurt'],
                    'kepala_ruta' => $value['kepala_ruta'],
                    'jumlah_art' => $value['jumlah_art'],
                    'jumlah_unit_usaha' => $value['jumlah_unit_usaha'],
                    'subsektor1_a' => $value['subsektor1_a'],
                    'subsektor1_b' => $value['subsektor1_b'],
                    'subsektor2_a' => $value['subsektor2_a'],
                    'subsektor2_b' => $value['subsektor2_b'],
                    'subsektor3_a' => $value['subsektor3_a'],
                    'subsektor3_b' => $value['subsektor3_b'],
                    'subsektor4_a' => $value['subsektor4_a'],
                    'subsektor4_b' => $value['subsektor4_b'],
                    'subsektor4_c' => $value['subsektor4_c'],
                    'subsektor5_a' => $value['subsektor5_a'],
                    'subsektor5_b' => $value['subsektor5_b'],
                    'subsektor5_c' => $value['subsektor5_c'],
                    'subsektor6_a' => $value['subsektor6_a'],
                    'subsektor6_b' => $value['subsektor6_b'],
                    'subsektor6_c' => $value['subsektor6_c'],
                    'subsektor7_a' => $value['subsektor7_a'],
                    'jml_308_sawah' => $value['jml_308_sawah'],
                    'jml_308_bukan_sawah' => $value['jml_308_bukan_sawah'],
                    'jml_308_rumput_sementara' => $value['jml_308_rumput_sementara'],
                    'jml_308_rumput_permanen' => $value['jml_308_rumput_permanen'],
                    'jml_308_belum_tanam' => $value['jml_308_belum_tanam'],
                    'jml_308_ternak_bangunan_lain' => $value['jml_308_ternak_bangunan_lain'],
                    'jml_308_kehutanan' => $value['jml_308_kehutanan'],
                    'jml_308_budidaya' => $value['jml_308_budidaya'],
                    'jml_308_lahan_lainnya' => $value['jml_308_lahan_lainnya'],
                    'jml_308_tanaman_tahunan' => $value['jml_308_tanaman_tahunan'],
                    'apakah_menggunakan_lahan' => $value['apakah_menggunakan_lahan'],
                    'status_data' => $value['status_data'],
                    'daftar_komoditas' => $value['daftar_komoditas'],
                    'start_time' => $value['start_time'],
                    'end_time' => $value['end_time'],
                    'start_latitude' => $value['start_latitude'],
                    'end_latitude' => $value['end_latitude'],
                    'start_longitude' => $value['start_longitude'],
                    'end_longitude' => $value['end_longitude'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];
            }
        }

        foreach ($data_store as $key => $value) {
            if ($value['id']) {
                $model = Ruta::find($value['id']);
                if($model!=null){
                    $model->update($value);
                }
                else{
                    $value['id']    = "";
                    Ruta::create($value);
                }
                // try {
                //     Ruta::findOrFail($value['id'])->update($value);
                // } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                //     $value['id'] = '';
                //     Ruta::create($value);
                // }
            } else {
                Ruta::create($value);
            }
        }

        foreach ($data_delete as $id) {
            Ruta::find($id)->delete();
        }

        return response()->json(['status' => 'success', 'data'=> "Data berhasil diupload"]);
    }


    /**
     * @OA\Post(
     *     path="/api/ruta/update_sls_many",
     *     tags={"ruta"},
     *     summary="Update SLS Many Ruta",
     *     description="Update SLS on many rutas data",
     *     operationId="ruta/update_sls_many",
     *     @OA\Parameter(
     *          name="Bearer Token",
     *          description="",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="form ruta",
     *          @OA\JsonContent(
     *              required={"kode_prov", "kode_kab", "kode_kec", "kode_desa", "id_sls", "id_sub_sls", "selected_data"},
     *              @OA\Property(property="kode_prov", type="string"),
     *              @OA\Property(property="kode_kab", type="string"),
     *              @OA\Property(property="kode_kec", type="string"),
     *              @OA\Property(property="kode_desa", type="string"),
     *              @OA\Property(property="id_sls", type="string"),
     *              @OA\Property(property="id_sub_sls", type="string"),
     *              @OA\Property(property="selected_data", type="array"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function update_sls_many(Request $request)
    {
        Validator::make($value, [
            'kode_prov' => 'required|string|max:2',
            'kode_kab' => 'required|string|max:2',
            'kode_kec' => 'required|string|max:3',
            'kode_desa' => 'required|string|max:3',
            'id_sls' => 'required|string|max:4',
            'id_sub_sls' => 'required|string|max:2',
            'selected_data' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'data' => $validator->errors()]);
        }

        foreach($request->selected_data as $value) {
            $decryptId = Crypt::decryptString($value);
            $data = Ruta::find($decryptId);
            if($data!=null){
                $data->kode_kab = $request->kode_kab;
                $data->kode_kec = $request->kode_kec;
                $data->kode_desa = $request->kode_desa;
                $data->kode_id_sls = $request->kode_id_sls;
                $data->kode_id_sub_sls = $request->kode_id_sub_sls;
            }
        }

        return response()->json(['status' => 'success', 'data'  => null]);
    }

    /**
     * @OA\Get(
     *     path="/api/ruta/{id}/show",
     *     tags={"ruta"},
     *     summary="Detail ruta",
     *     description="-",
     *     operationId="ruta/show",
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
        try {
            $decryptId = Crypt::decryptString($id);

            $data = Ruta::find($decryptId);

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['status' => 'error', 'data' => null]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ruta/{id}",
     *     tags={"ruta"},
     *     summary="Update Ruta",
     *     description="-",
     *     operationId="ruta/update",
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
     *          description="form master ruta",
     *          @OA\JsonContent(
     *              required={"kode_prov", "kode_kab", "kode_kec", "kode_desa", "id_sls", "id_sub_sls", "nurt"},
     *              @OA\Property(property="kode_prov", type="string"),
     *              @OA\Property(property="kode_kab", type="string"),
     *              @OA\Property(property="kode_kec", type="string"),
     *              @OA\Property(property="kode_desa", type="string"),
     *              @OA\Property(property="id_sls", type="string"),
     *              @OA\Property(property="id_sub_sls", type="string"),
     *              @OA\Property(property="nurt", type="integer"),
     *              @OA\Property(property="kepala_ruta", type="string"),
     *              @OA\Property(property="jumlah_art", type="integer"),
     *              @OA\Property(property="jumlah_unit_usaha", type="integer"),
     *              @OA\Property(property="subsektor1_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor1_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor2_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor2_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor3_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor3_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor4_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor4_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor4_c", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor5_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor5_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor5_c", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor6_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor6_b", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor6_c", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="subsektor7_a", type="integer", minimum = 0, maximum = 1),
     *              @OA\Property(property="jml_308_sawah", type="integer"),
     *              @OA\Property(property="jml_308_bukan_sawah", type="integer"),
     *              @OA\Property(property="jml_308_rumput_sementara", type="integer"),
     *              @OA\Property(property="jml_308_rumput_permanen", type="integer"),
     *              @OA\Property(property="jml_308_belum_tanam", type="integer"),
     *              @OA\Property(property="jml_308_ternak_bangunan_lain", type="integer"),
     *              @OA\Property(property="jml_308_kehutanan", type="integer"),
     *              @OA\Property(property="jml_308_budidaya", type="integer"),
     *              @OA\Property(property="jml_308_lahan_lainnya", type="integer"),
     *              @OA\Property(property="jml_308_tanaman_tahunan", type="integer"),
     *              @OA\Property(property="apakah_menggunakan_lahan", type="boolean"),
     *              @OA\Property(property="status_data", type="integer"),
     *              @OA\Property(property="daftar_komoditas", type="integer"),
     *              @OA\Property(property="start_time", type="datetime"),
     *              @OA\Property(property="end_time", type="datetime"),
     *              @OA\Property(property="start_latitude", type="numeric"),
     *              @OA\Property(property="end_latitude", type="numeric"),
     *              @OA\Property(property="start_longitude", type="numeric"),
     *              @OA\Property(property="end_longitude", type="numeric"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'data' => $validator->errors()]);
        }

        try {
            $decryptId = Crypt::decryptString($id);

            $data = Ruta::find($decryptId);

            $data->kode_prov = $request->kode_prov;
            $data->kode_kab = $request->kode_kab;
            $data->kode_kec = $request->kode_kec;
            $data->kode_desa = $request->kode_desa;
            $data->id_sls = $request->id_sls;
            $data->id_sub_sls = $request->id_sub_sls;
            $data->nurt = $request->nurt;
            $data->kepala_ruta = $request->kepala_ruta;
            $data->jumlah_art = $request->jumlah_art;
            $data->jumlah_unit_usaha = $request->jumlah_unit_usaha;
            $data->subsektor1_a = $request->subsektor1_a;
            $data->subsektor1_b = $request->subsektor1_b;
            $data->subsektor2_a = $request->subsektor2_a;
            $data->subsektor2_b = $request->subsektor2_b;
            $data->subsektor3_a = $request->subsektor3_a;
            $data->subsektor3_b = $request->subsektor3_b;
            $data->subsektor4_a = $request->subsektor4_a;
            $data->subsektor4_b = $request->subsektor4_b;
            $data->subsektor4_c = $request->subsektor4_c;
            $data->subsektor5_a = $request->subsektor5_a;
            $data->subsektor5_b = $request->subsektor5_b;
            $data->subsektor5_c = $request->subsektor5_c;
            $data->subsektor6_a = $request->subsektor6_a;
            $data->subsektor6_b = $request->subsektor6_b;
            $data->subsektor6_c = $request->subsektor6_c;
            $data->subsektor7_a = $request->subsektor7_a;
            $data->jml_308_sawah = $request->jml_308_sawah;
            $data->jml_308_bukan_sawah = $request->jml_308_bukan_sawah;
            $data->jml_308_rumput_sementara = $request->jml_308_rumput_sementara;
            $data->jml_308_rumput_permanen = $request->jml_308_rumput_permanen;
            $data->jml_308_belum_tanam = $request->jml_308_belum_tanam;
            $data->jml_308_ternak_bangunan_lain = $request->jml_308_ternak_bangunan_lain;
            $data->jml_308_kehutanan = $request->jml_308_kehutanan;
            $data->jml_308_budidaya = $request->jml_308_budidaya;
            $data->jml_308_lahan_lainnya = $request->jml_308_lahan_lainnya;
            $data->jml_308_tanaman_tahunan = $request->jml_308_tanaman_tahunan;
            $data->apakah_menggunakan_lahan = $request->apakah_menggunakan_lahan;
            $data->status_data = $request->status_data;
            $data->daftar_komoditas = $request->daftar_komoditas;
            $data->start_time = $request->start_time;
            $data->end_time = $request->end_time;
            $data->start_latitude = $request->start_latitude;
            $data->end_latitude = $request->end_latitude;
            $data->start_longitude = $request->start_longitude;
            $data->end_longitude = $request->end_longitude;
            $data->updated_by = Auth::id();
            $data->save();

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['status' => 'error', 'data' => null]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/ruta/{id}",
     *     tags={"ruta"},
     *     summary="Delete Ruta",
     *     description="-",
     *     operationId="ruta/delete",
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

            $data = Ruta::find($decryptId);

            $data->delete();

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['status' => 'error', 'data' => null]);
        }
    }

    private function validator($value)
    {
        return Validator::make($value, [
            'kode_prov' => 'required|string|max:2',
            'kode_kab' => 'required|string|max:2',
            'kode_kec' => 'required|string|max:3',
            'kode_desa' => 'required|string|max:3',
            'id_sls' => 'required|string|max:4',
            'id_sub_sls' => 'required|string|max:2',
            'nurt' => 'required|integer',
            'kepala_ruta' => 'string',
            'jumlah_art' => 'integer',
            'jumlah_unit_usaha' => 'integer',
            'subsektor1_a' => 'integer',
            'subsektor1_b' => 'integer',
            'subsektor2_a' => 'integer',
            'subsektor2_b' => 'integer',
            'subsektor3_a' => 'integer',
            'subsektor3_b' => 'integer',
            'subsektor4_a' => 'integer',
            'subsektor4_b' => 'integer',
            'subsektor4_c' => 'integer',
            'subsektor5_a' => 'integer',
            'subsektor5_b' => 'integer',
            'subsektor5_c' => 'integer',
            'subsektor6_a' => 'integer',
            'subsektor6_b' => 'integer',
            'subsektor6_c' => 'integer',
            'subsektor7_a' => 'integer',
            'jml_308_sawah' => 'integer',
            'jml_308_bukan_sawah' => 'integer',
            'jml_308_rumput_sementara' => 'integer',
            'jml_308_rumput_permanen' => 'integer',
            'jml_308_belum_tanam' => 'integer',
            'jml_308_ternak_bangunan_lain' => 'integer',
            'jml_308_kehutanan' => 'integer',
            'jml_308_budidaya' => 'integer',
            'jml_308_lahan_lainnya' => 'integer',
            'jml_308_tanaman_tahunan' => 'integer',
            'apakah_menggunakan_lahan' => 'boolean',
            'status_data' => 'integer',
            'daftar_komoditas' => 'string',
            'start_time' => 'string',
            'end_time' => 'string',
            'start_latitude' => 'numeric',
            'end_latitude' => 'numeric',
            'start_longitude' => 'numeric',
            'end_longitude' => 'numeric',
        ]);
    }


    public function delete_ruta_duplikat(Request $request)
    {
        $ruta = Ruta::select('kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls', 'nurt', 'kepala_ruta', DB::raw('COUNT(*) as jumlah_duplikat'))
            ->groupBy('kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls', 'nurt', 'kepala_ruta')
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('jumlah_duplikat')
            ->get();


        foreach ($ruta as $rt) {
            $data = Ruta::where('kode_kab', $rt->kode_kab)
                ->where('kode_kec', $rt->kode_kec)
                ->where('kode_desa', $rt->kode_desa)
                ->where('id_sls', $rt->id_sls)
                ->where('id_sub_sls', $rt->id_sub_sls)
                ->where('nurt', $rt->nurt)
                ->where('kepala_ruta', $rt->kepala_ruta)
                ->get();

            $idsToDelete = [];
            $skipFirstRow = true;

            foreach ($data as $dt) {
                if ($skipFirstRow) {
                    $skipFirstRow = false;
                    continue;
                }
                $idsToDelete[] = $dt->id;
            }
            Ruta::whereIn('id', $idsToDelete)->delete();
        }
        return response()->json(['status' => 'success', 'data' => "selesai"]);
    }
}

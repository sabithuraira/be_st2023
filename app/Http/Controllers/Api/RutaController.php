<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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

        if(isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;

        $condition = [];

        if(strlen($request->kode_wilayah) >= 2) $condition[] = ['kode_prov', '=', substr($request->kode_wilayah, 0, 2)];
        if(strlen($request->kode_wilayah) >= 4) $condition[] = ['kode_kab', '=', substr($request->kode_wilayah, 2, 2)];
        if(strlen($request->kode_wilayah) >= 7) $condition[] = ['kode_kec', '=', substr($request->kode_wilayah, 4, 3)];
        if(strlen($request->kode_wilayah) >= 10) $condition[] = ['kode_desa', '=', substr($request->kode_wilayah, 7, 3)];
        if(strlen($request->kode_wilayah) >= 14) $condition[] = ['id_sls', '=', substr($request->kode_wilayah, 10, 4)];
        if(strlen($request->kode_wilayah) >= 16) $condition[] = ['id_subsls', '=', substr($request->kode_wilayah, 14, 2)];

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
     *              required={"kode_prov", "kode_kab", "kode_kec", "kode_desa", "id_sls", "id_sub_sls", "start_time", "end_time", "start_latitude", "end_latitude", "start_longitude", "end_longitude"},
     *              @OA\Property(property="kode_prov", type="string"),
     *              @OA\Property(property="kode_kab", type="string"),
     *              @OA\Property(property="kode_kec", type="string"),
     *              @OA\Property(property="kode_desa", type="string"),
     *              @OA\Property(property="id_sls", type="string"),
     *              @OA\Property(property="id_sub_sls", type="string"),
     *              @OA\Property(property="start_time", type="datetime"),
     *              @OA\Property(property="end_time", type="datetime"),
     *              @OA\Property(property="start_latitude", type="decimal"),
     *              @OA\Property(property="end_latitude", type="decimal"),
     *              @OA\Property(property="start_longitude", type="decimal"),
     *              @OA\Property(property="end_longitude", type="decimal"),
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

        if ($validator->fails())
        {
            return response()->json(['status' => 'error', 'data' => $validator->errors()]);
        }

        $data = Ruta::create([
            'kode_prov' => $request->kode_prov,
            'kode_kab' => $request->kode_kab,
            'kode_kec' => $request->kode_kec,
            'kode_desa' => $request->kode_desa,
            'id_sls' => $request->id_sls,
            'id_sub_sls' => $request->id_sub_sls,
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
     *          description="in data, just send object with property kode_prov, kode_kab, kode_kec, kode_desa, id_sls, id_sub_sls, start_time, end_time, start_latitude, end_latitude, start_longitude, end_longitude",
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
        $data = [];

        foreach($request->data as $key => $value)
        {
            $validator = $this->validator($value);

            if ($validator->fails())
            {
                return response()->json(['status' => 'error', 'data' => $validator->errors(), 'at' => $value]);
            }

            try
            {
                if ($value['id']) $value['id'] = Crypt::decryptString($value['id']);
            }

            catch(\Illuminate\Contracts\Encryption\DecryptException $e)
            {
                return response()->json(['status' => 'error', 'data' => null, 'at' => $value]);
            }

            $data[] = [
                'id' => $value['id'],
                'kode_prov' => $value['kode_prov'],
                'kode_kab' => $value['kode_kab'],
                'kode_kec' => $value['kode_kec'],
                'kode_desa' => $value['kode_desa'],
                'id_sls' => $value['id_sls'],
                'id_sub_sls' => $value['id_sub_sls'],
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
        
        foreach($data as $key => $value)
        {
            if ($value['id'])
            {
                Ruta::find($value['id'])->update($value);
            }
            else
            {
                Ruta::create($value);
            }
        }

        return response()->json(['status' => 'success']);
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
        try
        {
            $decryptId = Crypt::decryptString($id);

            $data = Ruta::find($decryptId);

            return response()->json(['status' => 'success', 'data' => $data]);
        }

        catch(\Illuminate\Contracts\Encryption\DecryptException $e)
        {
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
     *              required={"kode_prov", "kode_kab", "kode_kec", "kode_desa", "id_sls", "id_sub_sls", "start_time", "end_time", "start_latitude", "end_latitude", "start_longitude", "end_longitude"},
     *              @OA\Property(property="kode_prov", type="string"),
     *              @OA\Property(property="kode_kab", type="string"),
     *              @OA\Property(property="kode_kec", type="string"),
     *              @OA\Property(property="kode_desa", type="string"),
     *              @OA\Property(property="id_sls", type="string"),
     *              @OA\Property(property="id_sub_sls", type="string"),
     *              @OA\Property(property="start_time", type="datetime"),
     *              @OA\Property(property="end_time", type="datetime"),
     *              @OA\Property(property="start_latitude", type="decimal"),
     *              @OA\Property(property="end_latitude", type="decimal"),
     *              @OA\Property(property="start_longitude", type="decimal"),
     *              @OA\Property(property="end_longitude", type="decimal"),
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

        if ($validator->fails())
        {
            return response()->json(['status' => 'error', 'data' => $validator->errors()]);
        }

        try
        {
            $decryptId = Crypt::decryptString($id);

            $data = Ruta::find($decryptId);

            $data->kode_prov = $request->kode_prov;
            $data->kode_kab = $request->kode_kab;
            $data->kode_kec = $request->kode_kec;
            $data->kode_desa = $request->kode_desa;
            $data->id_sls = $request->id_sls;
            $data->id_sub_sls = $request->id_sub_sls;
            $data->start_time = $request->start_time;
            $data->end_time = $request->end_time;
            $data->start_latitude = $request->start_latitude;
            $data->end_latitude = $request->end_latitude;
            $data->start_longitude = $request->start_longitude;
            $data->end_longitude = $request->end_longitude;
            $data->updated_by = Auth::id();
            $data->save();

            return response()->json(['status' => 'success', 'data' => $data]);
        }

        catch(\Illuminate\Contracts\Encryption\DecryptException $e)
        {
            return response()->json(['status' => 'error', 'data' => null ]);
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
        try
        {
            $decryptId = Crypt::decryptString($id);

            $data = Ruta::find($decryptId);

            $data->delete();

            return response()->json(['status' => 'success', 'data' => $data ]);
        }

        catch(\Illuminate\Contracts\Encryption\DecryptException $e)
        {
            return response()->json(['status' => 'error', 'data' => null ]);
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
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'start_latitude' => 'required',
            'end_latitude' => 'required',
            'start_longitude' => 'required',
            'end_longitude' => 'required'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RutaController extends Controller
{
    public function index(Request $request)
    {
        $per_page = 20;

        if(isset($request->per_page) && strlen($request->per_page) > 0){
            $per_page = $request->per_page;
        }

        $data = Ruta::orderBy('id', 'desc')->paginate($per_page);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = $this->validator($request);

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
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'start_latitude' => $request->start_latitude,
            'end_latitude' => $request->end_latitude,
            'start_longitude' => $request->start_longitude,
            'end_longitude' => $request->end_longitude,
            'created_by' => '2',
            'updated_by' => '2'
        ]);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function show($id)
    {
        $data = Ruta::find($id);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'data' => $validator->errors()]);
        }

        $request->merge(['updated_by' => '22']);

        $data = Ruta::find($id);

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
        $data->updated_by = "22";

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function destroy($id)
    {
        $data = Ruta::find($id);

        $data->delete();

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    private function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'kode_prov' => 'required',
            'kode_kab' => 'required',
            'kode_kec' => 'required',
            'kode_desa' => 'required',
            'id_sls' => 'required',
            'id_sub_sls' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_latitude' => 'required',
            'end_latitude' => 'required',
            'start_longitude' => 'required',
            'end_longitude' => 'required'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desas;
use App\Models\Kabs;
use App\Models\Kecs;
use App\Models\Sls;
use App\Models\PesSt2023;
use Illuminate\Http\Request;

class PesController extends Controller
{
    //

    public function index(Request $request)
    {
        $label_kab = "";
        $label_kec = "";
        // $condition[] = [];
        $condition[] = ['kode_kab', '<>', '00'];
        $label_kab = Kabs::where('id_kab', $request->kab_filter)
            ->pluck('nama_kab')->first();
        $label_kec = Kecs::where('id_kab', $request->kab_filter)
            ->where('id_kec', $request->kec_filter)
            ->pluck('nama_kec')->first();

        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $request->kec_filter];
        $data = PesSt2023::where($condition)
            ->paginate(20);

        $data->withPath('pes_st2023');
        $data->appends($request->all());
        return response()->json(['status' => 'success', 'data' => $data, 'label_kab' => $label_kab, 'label_kec' => $label_kec]);
    }
}

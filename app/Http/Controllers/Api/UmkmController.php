<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desas;
use App\Models\Kabs;
use App\Models\Kecs;
use App\Models\Ruta;
use App\Models\Sls;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $label_kab = "";
        $label_kec = "";
        $label_desa = "";
        $label_kab = Kabs::where('id_kab', $request->kab_filter)
            ->pluck('nama_kab')->first();
        $label_kec = Kecs::where('id_kab', $request->kab_filter)
            ->where('id_kec', $request->kec_filter)
            ->pluck('nama_kec')->first();
        $label_desa = Desas::where('id_kab', $request->kab_filter)
            ->where('id_kec', $request->kec_filter)
            ->where('id_desa', $request->desa_filter)
            ->pluck('nama_desa')->first();

        if ($request->desa_filter) {
            $data = Umkm::select(
                DB::raw(
                    "CONCAT(id_sls,id_sub_sls) as kode_sls, CONCAT(id_sls,id_sub_sls) as kode_wilayah"
                ),
                'nama_sls as nama_wilayah',
                DB::raw(
                    "1 as jml_sls"
                ),
                'status_selesai as status_selesai',
                'jml_kk as jml_kk',
                'no_urut_usaha_terbesar as jml_usaha',
                'jml_koperasi as jml_koperasi'
            )
                ->where('kode_kab', $request->kab_filter)
                ->where('kode_kec', $request->kec_filter)
                ->where('kode_desa', $request->desa_filter)

                ->get();
        } else if ($request->kec_filter) {
            $data = Umkm::where('kode_kab', $request->kab_filter)
                ->where('kode_kec', $request->kec_filter)
                ->where('id_kab', $request->kab_filter)
                ->where('id_kec', $request->kec_filter)
                ->groupBy('kode_desa', 'nama_desa')
                ->selectRaw('
                 kode_desa as kode_desa,
                 kode_desa as kode_wilayah,
                 desas.nama_desa as nama_wilayah,
                 count(*) as jml_sls,
                 sum(status_selesai) as status_selesai,
                 sum(jml_kk) as jml_kk,
                 sum(no_urut_usaha_terbesar) as jml_usaha,
                 sum(jml_koperasi) as jml_koperasi
                 ')
                ->join('desas', 'sls_umkm.kode_desa', '=', 'desas.id_desa')
                ->get();
        } else if ($request->kab_filter) {
            $data = Umkm::where('kode_kab', $request->kab_filter)
                ->where('id_kab', $request->kab_filter)
                ->groupBy('kode_kec', 'nama_kec')
                ->selectRaw('
                 kode_kec as kode_kec,
                 kode_kec as kode_wilayah,
                 kecs.nama_kec as nama_wilayah,
                 count(*) as jml_sls,
                 sum(status_selesai) as status_selesai,
                 sum(jml_kk) as jml_kk,
                 sum(no_urut_usaha_terbesar) as jml_usaha,
                 sum(jml_koperasi) as jml_koperasi
                 ')
                ->join('kecs', 'sls_umkm.kode_kec', '=', 'kecs.id_kec')
                ->get();
        } else {
            $data = Umkm::groupBy('kode_kab', 'nama_kab')
                ->selectRaw('
                 kode_kab as kode_kab,
                 kode_kab as kode_wilayah,
                 kabs.nama_kab as nama_wilayah,
                 count(*) as jml_sls,
                 sum(status_selesai) as status_selesai,
                 sum(jml_kk) as jml_kk,
                 sum(no_urut_usaha_terbesar) as jml_usaha,
                 sum(jml_koperasi) as jml_koperasi
                 ')
                ->join('kabs', 'sls_umkm.kode_kab', '=', 'kabs.id_kab')
                ->get();
        }
        return response()->json(['status' => 'success', 'data' => $data, 'label_kab' => $label_kab, 'label_kec' => $label_kec, 'label_desa' => $label_desa]);
    }
}

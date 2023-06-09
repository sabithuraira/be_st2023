<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desas;
use App\Models\Kabs;
use App\Models\Kecs;
use App\Models\Sls;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PetugasController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/petugas",
     *     tags={"petugas"},
     *     summary="Get List",
     *     description="pencarian dg keyword akan mengarah pada nama petugas",
     *     operationId="List Petugas",
     *     @OA\RequestBody(
     *          required=false,
     *          description="filter and pagination",
     *          @OA\JsonContent(
     *              @OA\Property(property="keyword", type="string"),
     *              @OA\Property(property="kab_filter", type="string"),
     *              @OA\Property(property="kec_filter", type="string"),
     *              @OA\Property(property="desa_filter", type="string"),
     *              @OA\Property(property="sls_filter", type="string"),
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
        $keyword = $request->keyword;
        $per_page = 20;
        $datas = [];
        $condition[] = ['kode_kab', '<>', '00'];
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $request->kec_filter];
        if (isset($request->desa_filter) && strlen($request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $request->desa_filter];
        //PAGINATION
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;
        //KEYWORD CONDITION
        if (isset($request->keyword) && strlen($request->keyword) > 0) {
            $datas = User::where($condition)
                ->where(
                    (function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', '%' . $keyword . '%')
                            ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                    })
                )
                ->withcount('sls_ppl as jumlah_sls_ppl')
                ->withcount('sls_pml as jumlah_sls_pml')
                ->withcount('sls_koseka as jumlah_sls_koseka')
                ->with('roles')
                ->orderBy('kode_kab', 'Asc')
                ->orderBy('id', 'DESC')->paginate($per_page);
        } else {
            $datas = User::where($condition)
                ->with('roles')
                ->withcount('sls_ppl as jumlah_sls_ppl')
                ->withcount('sls_pml as jumlah_sls_pml')
                ->withcount('sls_koseka as jumlah_sls_koseka')
                ->orderBy('kode_kab', 'Asc')
                ->orderBy('name', 'Asc')
                ->paginate($per_page);
        }
        $datas->withPath('petugas');
        $datas->appends($request->all());

        return response()->json(['status' => 'success', 'data' => $datas, 'label_kab' => $label_kab, 'label_kec' => $label_kec, 'label_desa' => $label_desa, 'label_sls' => $label_sls]);
    }

    public function rekap(Request $request)
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

        $keyword = $request->keyword;
        $per_page = 20;
        $datas = [];
        $condition = [];
        // $condition[] = ['kode_kab', '<>', '00'];
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        if (isset($request->kec_filter) && strlen($request->kec_filter) > 0) $condition[] = ['kode_kec', '=', $request->kec_filter];
        if (isset($request->desa_filter) && strlen($request->desa_filter) > 0) $condition[] = ['kode_desa', '=', $request->desa_filter];
        //PAGINATION
        if (isset($request->per_page) && strlen($request->per_page) > 0) $per_page = $request->per_page;
        $datas = User::where($condition)
            ->role(["PPL"])
            ->with('roles')
            ->withCount('rutas')
            ->withCount('sls_ppl as jml_sls')
            ->withSum('sls_ppl as prelist_ruta', 'ruta_prelist')
            ->withSum('sls_ppl as prelist_ruta_tani', 'prelist_ruta_tani')
            ->orderBy('kode_kab', 'ASC')
            ->orderBy('name', 'ASC')
            ->paginate($per_page);
        $datas->withPath('target');
        $datas->appends($request->all());
        return response()->json(['status' => 'success', 'data' => $datas, 'label_kab' => $label_kab, 'label_kec' => $label_kec, 'label_desa' => $label_desa, 'label_sls' => $label_sls]);
    }

    public function show($id)
    {
        $status = "success";

        try {
            $decryptId = Crypt::decryptString($id);
            $result = User::where('id', $decryptId)->with('roles')->first();
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
            $model = User::find($decryptId);
            $model->name = $request->name;
            $model->syncRoles([$request->roles]);
            $model->save();
            return response()->json(['status' => 'success', 'data' => $model]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['status' => "error", 'data' => null]);
        }
    }

    public function petugas_sls($id)
    {
        $per_page = 20;
        $condition = [];

        $user = User::where('email', $id)->first();

        // dd($user->hasRole('pml'));
        $condition = [];
        if ($user->hasRole('PPL')) $condition[] = ['kode_pcl', '=', $id];
        if ($user->hasRole('PML')) $condition[] = ['kode_pml', '=', $id];
        if ($user->hasRole('Koseka')) $condition[] = ['kode_koseka', '=', $id];


        $datas = Sls::select('master_sls.kode_kab', 'master_sls.kode_kec', 'master_sls.kode_desa', 'master_sls.id_sls', 'master_sls.id_sub_sls', 'status_selesai_pcl', 'jml_keluarga_tani', DB::raw('count(ruta.id) as jumlah_ruta'))
            ->where($condition)
            ->leftjoin('ruta', function ($join) {
                $join->on('master_sls.kode_kab', '=', 'ruta.kode_kab')
                    ->on('master_sls.kode_kec', '=', 'ruta.kode_kec')
                    ->on('master_sls.kode_desa', '=', 'ruta.kode_desa')
                    ->on('master_sls.id_sls', '=', 'ruta.id_sls')
                    ->on('master_sls.id_sub_sls', '=', 'ruta.id_sub_sls');
            })
            ->groupby('master_sls.kode_kab', 'master_sls.kode_kec', 'master_sls.kode_desa', 'master_sls.id_sls', 'master_sls.id_sub_sls', 'status_selesai_pcl', 'jml_keluarga_tani')
            ->paginate($per_page);

        return response()->json(['status' => 'success', 'data' => $datas]);
    }

    public function getcsrf(Request $request)
    {
        // Mendapatkan token CSRF
        $token = $request->session()->token();
        return response()->json(['csrf_token' => $token]);
    }

    /**
     * @OA\Delete(
     *     path="/api/petugas/{id}",
     *     tags={"petugas"},
     *     summary="Delete petugas",
     *     description="-",
     *     operationId="petugas/delete",
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
            // $decryptId = Crypt::decryptString($id);
            $model = User::find($id);
            $model->delete();

            return response()->json(['status' => 'success', 'data' => "Data berhasil dihapus"]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['status' => 'error', 'data' => null]);
        }
    }

    public function list_petugas(Request $request)
    {
        $condition = [];
        if (isset($request->kab_filter) && strlen($request->kab_filter) > 0) $condition[] = ['kode_kab', '=', $request->kab_filter];
        $petugas = User::where($condition)->role('PPL')->orderBy('name', 'Asc')->get();
        $pcl = User::where($condition)->role('PPL')->orderBy('name', 'Asc')->get();
        $pml = User::where($condition)->role('PML')->orderBy('name', 'Asc')->get();
        $koseka = User::where($condition)->role('Koseka')->orderBy('name', 'Asc')->get();
        $data = [
            'list_pcl' => $pcl,
            'list_pml' => $pml,
            'list_koseka' => $koseka
        ];

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function list_roles(Request $request)
    {
        $data = Role::whereNotIn('name', ['Admin Provinsi', 'Super Admin'])->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}

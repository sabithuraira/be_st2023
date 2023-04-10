<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\AlokasiImport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class ImportController extends Controller
{

    public function authenticate(Request $request)
    {
        $credentials = [
            'email' => 'admin@bpssumsel.com',
            'password' => "@bps1600",
        ];
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $auth = Auth::user();
            $token = $request->user()->createToken("be_st2023");

            echo  $token->plainTextToken;
        } else {
            echo "Error";
        }
    }

    /**
     * @OA\Post(
     *     path="/api/import_user",
     *     tags={"import"},
     *     summary="Import User",
     *     description="Import Daftar user (Daftar Bisa dari hasil Export Manajemen Mitra)",
     *     operationId="import_user",
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
     *         description="File to upload",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"import_file"},
     *                @OA\Property(property="import_file", type="file"),
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File uploaded successfully"
     *     )
     * )
     */

    public function import_user(Request $request)
    {
        if ($request->file('import_file')) {
            Excel::import(new UsersImport, request()->file('import_file'));
            // return 'Berhasil Memasukkan data';
            return redirect()->back()->with('success', 'Berhasil Memasukkan data');
        } else {
            // return 'Kesalahan File';
            return redirect()->back()->with('error', 'Kesalahan File');
        }
    }


    /**
     * @OA\Post(
     *     path="/api/impot_alokasi",
     *     tags={"import"},
     *     summary="Import Alokasi Petugas",
     *     description="Import Daftar SLS yang sudah berisi alokasi petugas (PCL, PML, Koseka), bisa dari export SLS pada halaman yang sama",
     *     operationId="import_alokasi",
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
     *         description="File to upload",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"import_file"},
     *                @OA\Property(property="import_file", type="file"),
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File uploaded successfully"
     *     )
     * )
     */
    public function import_alokasi(Request $request)
    {
        if ($request->file('import_file')) {
            Excel::import(new AlokasiImport, request()->file('import_file'));
            // return 'Berhasil Memasukkan data';
            return redirect()->back()->with('success', 'Berhasil Memasukkan data');
        } else {
            // return 'Kesalahan File';
            return redirect()->back()->with('error', 'Kesalahan File');
        }
    }


    // public function make_roles()
    // {
    //     $role = Role::create(['name' => 'PPL']);
    //     $role = Role::create(['name' => 'PML']);
    //     $role = Role::create(['name' => 'Koseka']);
    //     $role = Role::create(['name' => 'Super Admin']);
    //     $role = Role::create(['name' => 'Admin Provinsi']);
    //     $role = Role::create(['name' => 'Admin Kabupaten']);
    //     return "selesai";
    // }
}

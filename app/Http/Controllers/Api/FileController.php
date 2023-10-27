<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class FileController extends Controller
{
    
    public function upload(Request $request, $id)
    {
        if($request->file('file_data')){
            $file = $request->file('file_data');
            $nama_file = time().str_replace(" ", "", $file->getClientOriginalName());

            $file->move($id, $nama_file);

            return response()->json(['status' => 'success', 'datas' => $id.'/'.$nama_file]);
        }

        return response()->json(['status' => 'error', 'datas' => "Upload Gagal"]);
    }
}

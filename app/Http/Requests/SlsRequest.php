<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'kode_prov' => 'required|string|max:2',
            'kode_kab' => 'required|string|max:2',
            'kode_kec' => 'required|string|max:3',
            'kode_desa' => 'required|string|max:3',
            'id_sls' => 'required|string|max:4',
            'id_sub_sls' => 'required|string|max:2',
            'nama_sls' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            // 'butir_kegiatan.required' => ':attribute tidak boleh kosong',
            // 'jenis.unique' => ':attribute sudah ada',
            // 'satuan_hasil.required' => ':attribute tidak boleh kosong',
            // 'kode.required' => ':attribute tidak boleh kosong',
            // 'angka_kredit.required' => ':attribute tidak boleh kosong',
            // 'pelaksana.required' => ':attribute tidak boleh kosong',
            // 'bukti_fisik.required' => ':attribute tidak boleh kosong',
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Sls;

class SlsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request){
        return [
            'id' => $this->id,
            'encId' => $this->encId,
            'kode_prov' => $this->kode_prov,
            'kode_kab' => $this->kode_kab,
            'kode_kec' => $this->kode_kec,
            'kode_desa' => $this->kode_desa,

            "id_sls"=> $this->id_sls,
            "id_sub_sls"=> $this->id_sub_sls,
            "nama_sls"=> $this->nama_sls,
            "sls_op"=> $this->sls_op,            
            "jenis_sls"=> $this->jenis_sls,
            
            "jml_art_tani"=> $this->jml_art_tani,
            "jml_keluarga_tani"=> $this->jml_keluarga_tani,
            "sektor1"=> $this->sektor1,
            "sektor2"=> $this->sektor2,
            "sektor3"=> $this->sektor3,
            "sektor4"=> $this->sektor4,
            "sektor5"=> $this->sektor5,
            "sektor6"=> $this->sektor6,

            "jml_keluarga_tani_st2023"=> $this->jml_keluarga_tani_st2023,
            "jml_nr"=> $this->jml_nr,

            "jml_dok_ke_pml"=> $this->jml_dok_ke_pml,
            "jml_dok_ke_koseka"=> $this->jml_dok_ke_koseka,
            "jml_dok_ke_bps"=> $this->jml_dok_ke_bps,

            "status_selesai_pcl"=> $this->status_selesai_pcl,
            
            "kode_pcl"=> $this->kode_pcl,
            "kode_pml"=> $this->kode_pml,
            "kode_koseka"=> $this->kode_koseka,
            
            "status_sls"=> $this->status_sls,
            "created_by"=> $this->created_by,
            "updated_by"=> $this->updated_by,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at,
            "pendampingan_pml" => $this->pendampingan_pml,
            "pendampingan_koseka" => $this->pendampingan_koseka,
            "nama_desa" => $this->nama_desa,
            "nama_kec" => $this->nama_kec,
            "daftar_ruta"  => $this->rutas
        ];
    }
}

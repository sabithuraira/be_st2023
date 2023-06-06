<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Ruta extends Model
{
    use \Awobaz\Compoships\Compoships, HasFactory;
    protected $table = 'ruta';
    protected $fillable = [
        'kode_prov',
        'kode_kab',
        'kode_kec',
        'kode_desa',
        'id_sls',
        'id_sub_sls',
        'nurt',
        'kepala_ruta',
        'jumlah_art',
        'jumlah_unit_usaha',
        'subsektor1_a',
        'subsektor1_b',
        'subsektor2_a',
        'subsektor2_b',
        'subsektor3_a',
        'subsektor3_b',
        'subsektor4_a',
        'subsektor4_b',
        'subsektor4_c',
        'subsektor5_a',
        'subsektor5_b',
        'subsektor5_c',
        'subsektor6_a',
        'subsektor6_b',
        'subsektor6_c',
        'subsektor7_a',
        'jml_308_sawah',
        'jml_308_bukan_sawah',
        'jml_308_rumput_sementara',
        'jml_308_rumput_permanen',
        'jml_308_belum_tanam',
        'jml_308_ternak_bangunan_lain',
        'jml_308_kehutanan',
        'jml_308_budidaya',
        'jml_308_lahan_lainnya',
        'jml_308_tanaman_tahunan',
        'apakah_menggunakan_lahan',
        'status_data',
        'daftar_komoditas',
        'start_time',
        'end_time',
        'start_latitude',
        'end_latitude',
        'start_longitude',
        'end_longitude',
        'created_by',
        'updated_by'
    ];

    protected $appends = ['encId'];

    public function getEncIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }

    // public function kab()
    // {
    //     return $this->hasOne(
    //         Kabs::class,
    //         ['id_kab'],
    //         ['kode_kab']
    //     );
    // }
    public function kab()
    {
        return $this->belongsTo(Kabs::class, 'kode_kab', 'id_kab');
    }
    public function kec()
    {
        return $this->belongsTo(Kecs::class, ['kode_kab', 'kode_kec'], ['id_kab', 'id_kec']);
    }
    public function desa()
    {
        return $this->belongsTo(Desas::class, ['kode_kab', 'kode_kec', 'kode_desa'], ['id_kab', 'id_kec', 'id_desa']);
    }
    public function sls()
    {
        return $this->belongsTo(Sls::class, ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls'], ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls']);
    }
}

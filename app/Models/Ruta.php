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
}

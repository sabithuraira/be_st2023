<?php

namespace App\Models;

use \Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\Ruta;

class Desas extends Model
{

    use \Awobaz\Compoships\Compoships, HasFactory;
    protected $table = 'desas';

    public function sls()
    {
        return $this->hasMany(
            Sls::class,
            ['kode_kab', 'kode_kec', 'kode_desa'],
            ['id_kab', 'id_kec', 'id_desa']
        );
    }

    public function ruta()
    {
        return $this->hasMany(
            Ruta::class,
            ['kode_kab', 'kode_kec', 'kode_desa'],
            ['id_kab', 'id_kec', 'id_desa']
        );
    }
}

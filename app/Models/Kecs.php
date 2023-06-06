<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecs extends Model
{

    use \Awobaz\Compoships\Compoships, HasFactory;
    protected $table = 'kecs';

    public function sls()
    {
        return $this->hasMany(
            Sls::class,
            ['kode_kab', 'kode_kec'],
            ['id_kab', 'id_kec']
        );
    }

    public function ruta()
    {
        return $this->hasMany(
            Ruta::class,
            ['kode_kab', 'kode_kec'],
            ['id_kab', 'id_kec']
        );
    }
}

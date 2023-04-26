<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;


class Sls extends Model
{

    use \Awobaz\Compoships\Compoships, HasFactory;
    protected $table = 'master_sls';
    protected $appends = ['encId'];

    public function getEncIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }

    public function sektor1_desa()
    {
        return $this->hasMany(Ruta::class, ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls'], ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls'])
            ->where('sektor', 1);
    }

    public function sektor1_kec()
    {
        return $this->hasMany(Ruta::class, ['kode_kab', 'kode_kec', 'kode_desa'], ['kode_kab', 'kode_kec', 'kode_desa'])
            ->where('sektor', 1);
    }
}

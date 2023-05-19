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
    protected $guarded = [];

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

    public function getRutasAttribute()
    {
        $condition = [];
        $condition[] = ['kode_prov', '=', $this->kode_prov];
        $condition[] = ['kode_kab', '=', $this->kode_kab];
        $condition[] = ['kode_kec', '=', $this->kode_kec];
        $condition[] = ['kode_desa', '=', $this->kode_desa];
        $condition[] = ['id_sls', '=', $this->id_sls];
        $condition[] = ['id_sub_sls', '=', $this->id_sub_sls];

        return Ruta::where($condition)
            ->orderBy('nurt')
            ->get();
    }

    public function ruta()
    {
        return $this->hasMany(Ruta::class,  ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls'], [$this->kode_kab, $this->kec, $this->kode_desa, $this->id_sls]);
    }
}

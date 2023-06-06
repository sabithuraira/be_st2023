<?php

namespace App\Models;

use \Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\Ruta;


class Sls extends Model
{
    // use HasFactory;
    use \Awobaz\Compoships\Compoships, HasFactory;
    protected $table = 'master_sls';
    protected $appends = ['encId'];
    protected $guarded = [];

    public function getEncIdAttribute()
    {
        return Crypt::encryptString($this->id);
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
        return $this->hasMany(
            Ruta::class,
            ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls'],
            ['kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls']
        );
    }
}

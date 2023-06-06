<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabs extends Model
{

    use \Awobaz\Compoships\Compoships, HasFactory;
    protected $table = 'kabs';

    public function sls_selesai()
    {
        return $this->hasMany(Sls::class, 'id_kab', 'kode_kab')->where('status_selesai_pcl', 1);
    }
    public function sls()
    {
        return $this->hasMany(Sls::class, 'kode_kab', 'id_kab');
    }

    public function ruta()
    {
        return $this->hasMany(Ruta::class, 'kode_kab', 'id_kab');
    }
}

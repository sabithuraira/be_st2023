<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $guarded = [];
    protected $appends = ['encId'];

    public function getEncIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sls()
    {
        return $this->hasMany(Sls::class, 'kode_koseka', 'email')
            ->select('kode_kab', 'kode_kec', 'kode_desa', 'id_sls', 'id_sub_sls', 'kode_koseka');
    }

    public function rutas()
    {
        return $this->hasMany('App\Models\Ruta', 'created_by', 'id');
    }
}

<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

class AlokasiImport implements ToModel
{
    public function startRow(): int
    {
        return 2;
    }
    public function model(array $row)
    {

        $auth = Auth::user();

        $kd_wilayah = $auth->kd_wilayah;
        // khusus user Prov
        if ($auth->kd_wilayah == 00) {
            $kd_wilayah = $row[6];
        }
        if ($row[3] == 2) {
            return null;
        }
        // cek sudah ada di db atau belum
        $assign = User::where('email', $row[4])->first();
        if ($assign) {
            // jika sudah ada di db
            $user = new User([
                'name'     => $row[1],
                'email'    => $row[4],
                'password' => Hash::make('123456'),
                'pengawas' => $row[6],
                'kode_kab' => $kd_wilayah,
                'kode_kec' => $row[7],
                'kode_desa' => $row[8],
                'created_by' => $assign->created_by,
                'created_at' => $assign->created_at,
                'updated_by' => $auth->id
            ]);
        } else {
            $user = User::create([
                'name'     => $row[1],
                'email'    => $row[4],
                'password' => Hash::make('123456'),
                'pengawas' => $row[6],
                'kode_kab' => $kd_wilayah,
                'kode_kec' => $row[7],
                'kode_desa' => $row[8],
                'created_by' => $auth->id
            ]);
        }
        if (in_array($row[2], ['PPL', 'PML', 'Koseka'])) {
            if ($assign) {
                // jika dia ada di db
                $assign->syncRoles($row[2]);
            } else {
                $user->assignRole($row[2]);
            }
        }
        return $user;
    }
}

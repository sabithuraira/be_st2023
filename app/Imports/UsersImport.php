<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class UsersImport implements ToModel, WithStartRow, WithUpserts
{
    public function startRow(): int
    {
        return 2;
    }
    public function uniqueBy()
    {
        return 'email';
    }
    public function model(array $row)
    {
        $auth = Auth::user();
        $kd_wilayah = $auth->kd_wilayah;
        // khusus user Prov
        if ($auth->kd_wilayah == 00) {
            $kd_wilayah = $row[6];
        }
        if ($row[3] == "2" || $row[3] == "") {
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
        if (in_array($row[2], ['Petugas Lapangan Sensus', 'Pemeriksa Lapangan Sensus', 'Koseka', 'Admin Kabupaten'])) {
            $role = "";
            if ($row[2] == "Petugas Lapangan Sensus") {
                $role = "PPL";
            } else if ($row[2] == "Pemeriksa Lapangan Sensus") {
                $role = "PML";
            } else if ($row[2] == "Koseka") {
                $role = "Koseka";
            } else if ($row[2] == "Admin Kabupaten") {
                $role = "Admin Kabupaten";
            }

            if ($assign) {
                // jika dia ada di db
                $assign->syncRoles($role);
            } else {
                $user->assignRole($role);
            }
        }
        return $user;
    }
}

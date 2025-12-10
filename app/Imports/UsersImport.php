<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon; // Pastikan Carbon diimpor

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. Definisikan Kunci Pencarian Unik (kriteria WHERE)
        $searchKeys = [];

        // Prioritaskan employee_id
        if (!empty($row['employee_id'])) {
            $searchKeys['employee_id'] = $row['employee_id'];
        }
        // Jika employee_id kosong, gunakan email
        elseif (!empty($row['email'])) {
            $searchKeys['email'] = $row['email'];
        }
        // Jika employee_id kosong, gunakan email
        elseif (!empty($row['name'])) {
            $searchKeys['name'] = $row['name'];
        }

        // Jika tidak ada kunci unik yang valid, lewati baris
        if (empty($searchKeys)) {
            return null;
        }

        // 2. Panggil firstOrNew untuk mencari atau membuat instance baru
        $user = User::firstOrNew($searchKeys);

        // 3. Siapkan Data Dasar yang Akan Disisipkan atau Diperbarui
        // Gunakan fungsi parseDate() untuk memastikan format yyyy-mm-dd
        $dataToUpdate = [
            'name'                => $row['name'],
            'email'               => $row['email'] ?? null,
            'gender'              => $row['gender'] ?? null,
            'date_birth'          => $this->parseDate($row['date_birth'] ?? null),
            'department_name'     => $row['department_name'] ?? null,
            'employee_id'         => $row['employee_id'] ?? null,
            'date_commenced'      => $this->parseDate($row['date_commenced'] ?? null),
            'role_id'             => $row['role_id'] ?? null,
            'updated_at'          => now(),
        ];

        // 4. Logika Update atau Insert
        if ($user->exists) {
            // Data DITEMUKAN (UPDATE KONDISIONAL)

            // a. Update Username jika saat ini kosong di DB
            if (empty($user->username) && !empty($row['username'])) {
                $user->username = $row['username'];
            }

            // b. Update Password jika saat ini kosong di DB (Tanpa Hashing)
            // Ganti 'password_kolom' dengan nama header yang benar di Excel Anda
            if (empty($user->password) && !empty($row['password_kolom'])) {
                $user->password = $row['password_kolom'];
            }

            // c. Update data lainnya
            $user->fill($dataToUpdate);

            $user->save();
            return $user;

        } else {
            // Data BARU (INSERT)

            $dataToUpdate['username'] = $row['username'] ?? null;
            $dataToUpdate['created_at'] = now();

            // Tambahkan password untuk data baru (Plain Text)
            // Ganti 'password_kolom'
            if (!empty($row['password_kolom'])) {
                $dataToUpdate['password'] = $row['password_kolom'];
            } else {
                 $dataToUpdate['password'] = 'default_password';
            }

            // Gunakan create untuk menyisipkan data baru
            return User::create($dataToUpdate);
        }
    }

    /**
     * @param string|null $value
     * @return string|null
     * Memastikan tanggal disimpan dalam format yyyy-mm-dd
     */
    private function parseDate($value)
    {
        // Kalau kosong atau string "NULL" -> return null
        if (empty($value) || strtoupper($value) === 'NULL') {
            return null;
        }

        try {
            // Carbon::parse akan menangani format berbeda,
            // lalu format 'Y-m-d' akan memaksa output menjadi yyyy-mm-dd
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // fallback aman jika parsing gagal
        }
    }

    // Metode rules() Anda
    public function rules(): array
    {
         return [
             'name' => ['nullable'],
             'email' => ['nullable', 'email'],
             // ... (aturan validasi lainnya)
         ];
    }
}

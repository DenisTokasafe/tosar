<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    // ... (rules() dan parseDate() tetap sama) ...

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. Definisikan Kunci Pencarian Unik (kriteria WHERE)
        $searchKeys = [
            'employee_id' => $row['employee_id'] ?? null,
        ];

        // Cek juga berdasarkan email jika employee_id tidak ada
        if (empty($searchKeys['employee_id']) && !empty($row['email'])) {
             $searchKeys = ['email' => $row['email']];
        }

        // Jika tidak ada kunci unik yang ditemukan (misal baris kosong), lewati
        if (empty($searchKeys) || current($searchKeys) === null) {
            return null;
        }

        // 2. Siapkan Data yang Akan Disisipkan atau Diperbarui
        // Catatan: Jika Anda tidak ingin kolom-kolom ini ditimpa saat update,
        // Anda harus memindahkannya ke dalam logika kondisional di langkah 3.
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

        // 3. Panggil firstOrNew
        $user = User::firstOrNew($searchKeys);

        // Jika User sudah ada, kita hanya update kolom tertentu
        if ($user->exists) {

            // --- Logika Update Kondisional (Tanpa Hash) ---

            // Update Username jika saat ini kosong di DB
            if (empty($user->username) && !empty($row['username'])) {
                $user->username = $row['username'];
            }

            // Update Password jika saat ini kosong di DB
            // (Mengambil plain text dari Excel)
            if (empty($user->password) && !empty($row['password_kolom'])) {
                $user->password = $row['password_kolom']; // TIDAK menggunakan Hash::make()
            }

            // Update data lainnya yang selalu ingin di-update
            $user->fill($dataToUpdate);

            $user->save();
            return $user;

        } else {
            // Jika User BARU (tidak ditemukan), lakukan INSERT
            $dataToUpdate['username'] = $row['username'] ?? null;
            $dataToUpdate['created_at'] = now();

            // --- Logika Insert Data Baru (Tanpa Hash) ---

            // Tambahkan password untuk data baru (plain text)
            if (!empty($row['password_kolom'])) {
                $dataToUpdate['password'] = $row['password_kolom']; // TIDAK menggunakan Hash::make()
            } else {
                 $dataToUpdate['password'] = 'default_password'; // Password default plain text
            }

            // Gunakan `create` untuk menyisipkan data baru
            return User::create($dataToUpdate);
        }
    }
}

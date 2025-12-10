<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. Normalisasi Data Kunci
        $employeeId = $row['employee_id'] ?? null;
        $email = $row['email'] ?? null;
        $username = $row['username'] ?? null; // DITAMBAHKAN: Ambil nilai username
        $name = $row['name'] ?? null;

        // Normalisasi untuk pencarian yang ketat
        $normalizedEmployeeId = !empty($employeeId) ? trim((string)$employeeId) : null;
        $normalizedEmail = !empty($email) ? strtolower(trim($email)) : null;
        $normalizedUsername = !empty($username) ? strtolower(trim($username)) : null; // DITAMBAHKAN: Normalisasi username (opsional: strtolower)
        $normalizedName = !empty($name) ? trim($name) : null;


        // 2. Lakukan PENCARIAN EKSPLISIT (Prioritas: ID > Email > Username > Nama)
        $user = null;

        // PRIORITAS 4: Cari berdasarkan Name (Hanya jika belum ditemukan, berisiko penimpaan)
        if (is_null($user) && !empty($normalizedName)) {
            $user = User::where('name', $normalizedName)->first();
        }
        // PRIORITAS 3: Cari berdasarkan Username (DITAMBAHKAN: Hanya jika belum ditemukan)
        if (is_null($user) && !empty($normalizedUsername)) {
            $user = User::where('username', $normalizedUsername)->first();
        }

        // PRIORITAS 1: Cari berdasarkan Employee ID
        if (!empty($normalizedEmployeeId)) {
            $user = User::where('employee_id', $normalizedEmployeeId)->first();
        }

        // PRIORITAS 2: Cari berdasarkan Email (Hanya jika belum ditemukan)
        if (is_null($user) && !empty($normalizedEmail)) {
            $user = User::where('email', $normalizedEmail)->first();
        }


        // 3. Jika user tidak ditemukan, buat instance baru untuk disisipkan
        if (is_null($user)) {
             $user = new User();
             $user->exists = false; // Tandai sebagai data baru
        }


        // 4. Siapkan Data Dasar yang Akan Disisipkan atau Diperbarui
        // Gunakan nilai yang sudah dinormalisasi
        $dataToUpdate = [
            'name'                => $normalizedName,
            'email'               => $normalizedEmail,
            'gender'              => $this->mapGender($row['gender'] ?? null),
            'date_birth'          => $this->parseDate($row['date_birth'] ?? null),
            'department_name'     => $row['department_name'] ?? null,
            'employee_id'         => $normalizedEmployeeId,
            'date_commenced'      => $this->parseDate($row['date_commenced'] ?? null),
            'role_id'             => $row['role_id'] ?? null,
            'updated_at'          => now(),
            // Pastikan username masuk ke dataToUpdate
            'username'            => $normalizedUsername,
        ];


        // 5. Logika Update atau Insert
        if ($user->exists) {
            // Data DITEMUKAN (UPDATE / REPLACE)

            // a. Update Username jika saat ini kosong di DB (LOGIKA INI DIGANTI KARENA SUDAH ADA DI $dataToUpdate)
            // KECUALI Anda ingin mengabaikan pembaruan username jika sudah ada di DB.
            // Jika Anda ingin mempertahankan logika "isi hanya jika kosong", Anda bisa menghapus 'username' dari $dataToUpdate di atas, dan kembalikan logika ini:

            // -- Logika Update Kondisional Username --
            if (empty($user->username) && !empty($normalizedUsername)) {
                 $user->username = $normalizedUsername;
            }

            // b. Update Password jika saat ini kosong di DB (Plain Text)
            // Ganti 'password_kolom' dengan nama header yang benar di Excel Anda
            if (empty($user->password) && !empty($row['password_kolom'])) {
                $user->password = $row['password_kolom'];
            }

            // c. Update data lainnya
            // PENTING: Jika username DITAMBAHKAN ke $dataToUpdate, hapus logika kondisional username di atas.
            // Saya asumsikan Anda ingin username HANYA diisi jika kosong (seperti password)

            // Hapus 'username' dari dataToUpdate agar tidak menimpa nilai yang sudah ada di DB
            unset($dataToUpdate['username']);

            $user->fill($dataToUpdate);
            $user->save();
            return $user;

        } else {
            // Data BARU (INSERT)

            // Username sudah ada di $dataToUpdate

            $dataToUpdate['created_at'] = now();

            // Tambahkan password untuk data baru (Plain Text)
            if (!empty($row['password_kolom'])) {
                $dataToUpdate['password'] = $row['password_kolom'];
            } else {
                 $dataToUpdate['password'] = 'default_password';
            }

            // Lakukan INSERT
            return User::create($dataToUpdate);
        }
    }

    // --- Metode Pembantu (parseDate, mapGender, rules) ---

    private function parseDate($value)
    {
        if (empty($value) || strtoupper($value) === 'NULL') {
            return null;
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function mapGender($value)
    {
        if (empty($value)) {
            return null;
        }
        $normalizedValue = strtolower(trim($value));
        if (in_array($normalizedValue, ['l', 'male', 'laki-laki', 'pria'])) {
            return 'L';
        }
        if (in_array($normalizedValue, ['p', 'female', 'perempuan', 'wanita'])) {
            return 'P';
        }
        return null;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable'],
            'email' => ['nullable', 'email'],
            // ... (lanjutkan aturan validasi lainnya)
        ];
    }
}

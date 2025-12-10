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
        $name = $row['name'] ?? null;

        // Normalisasi untuk pencarian yang ketat (trim dan strtolower untuk email)
        $normalizedEmployeeId = !empty($employeeId) ? trim((string)$employeeId) : null;
        $normalizedEmail = !empty($email) ? strtolower(trim($email)) : null;
        $normalizedName = !empty($name) ? trim($name) : null;


        // 2. Lakukan PENCARIAN EKSPLISIT (Prioritas: ID > Email > Nama)
        $user = null;

        // PRIORITAS 1: Cari berdasarkan Employee ID
        if (!empty($normalizedEmployeeId)) {
            $user = User::where('employee_id', $normalizedEmployeeId)->first();
        }

        // PRIORITAS 2: Cari berdasarkan Email (Hanya jika belum ditemukan)
        if (is_null($user) && !empty($normalizedEmail)) {
            $user = User::where('email', $normalizedEmail)->first();
        }

        // PRIORITAS 3: Cari berdasarkan Name (Hanya jika belum ditemukan, berisiko penimpaan)
        if (is_null($user) && !empty($normalizedName)) {
            $user = User::where('name', $normalizedName)->first();
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
        ];


        // 5. Logika Update atau Insert
        if ($user->exists) {
            // Data DITEMUKAN (UPDATE / REPLACE)

            // a. Update Username jika saat ini kosong di DB
            if (empty($user->username) && !empty($row['username'])) {
                $user->username = $row['username'];
            }

            // b. Update Password jika saat ini kosong di DB (Plain Text)
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

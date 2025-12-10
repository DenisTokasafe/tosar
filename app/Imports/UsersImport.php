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
        $employeeId = !empty($row['employee_id']) ? trim($row['employee_id']) : null;
        $email = !empty($row['email']) ? strtolower(trim($row['email'])) : null;
        $name = !empty($row['name']) ? trim($row['name']) : null; // Ambil dan bersihkan Nama

        $searchKeys = [];

        // Prioritas Pencarian (Dari yang Paling Unik ke Paling Tidak Unik)

        // A. PRIORITAS 1: employee_id
        if (!empty($employeeId)) {
            $searchKeys['employee_id'] = $employeeId;
        }

        // B. PRIORITAS 2: email
        // Hanya jika employee_id tidak ada
        elseif (!empty($email)) {
            $searchKeys['email'] = $email;
        }

        // C. PRIORITAS 3 (FALLBACK): name
        // Hanya jika employee_id DAN email kosong.
        // Risiko: Jika nama tidak unik, data akan menimpa user pertama yang ditemukan.
        elseif (!empty($name)) {
            $searchKeys['name'] = $name;
        }

        // Jika tidak ada kunci pencarian yang valid, lewati baris
        if (empty($searchKeys)) {
            return null;
        }

        // 2. Panggil firstOrNew
        $user = User::firstOrNew($searchKeys);

        // 3. Siapkan Data Dasar yang Akan Disisipkan atau Diperbarui
        // Gunakan nilai yang sudah dinormalisasi
        $dataToUpdate = [
            'name'                => $name,
            'email'               => $email,
            'gender'              => $this->mapGender($row['gender'] ?? null),
            'date_birth'          => $this->parseDate($row['date_birth'] ?? null),
            'department_name'     => $row['department_name'] ?? null,
            'employee_id'         => $employeeId,
            'date_commenced'      => $this->parseDate($row['date_commenced'] ?? null),
            'role_id'             => $row['role_id'] ?? null,
            'updated_at'          => now(),
        ];

        // 4. Logika Update atau Insert
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

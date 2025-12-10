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
        // 1. Normalisasi Data Kunci (Penting untuk menghindari error Duplicate entry)
        $employeeId = !empty($row['employee_id']) ? trim($row['employee_id']) : null;
        // Email dinormalisasi ke huruf kecil untuk pencarian yang konsisten
        $email = !empty($row['email']) ? strtolower(trim($row['email'])) : null;

        $searchKeys = [];

        // Prioritaskan employee_id untuk pencarian (Lebih unik)
        if (!empty($employeeId)) {
            $searchKeys['employee_id'] = $employeeId;
        }
        // Jika employee_id kosong, gunakan email (Kunci unik kedua)
        elseif (!empty($email)) {
            $searchKeys['email'] = $email;
        }

        // --- HAPUS PENCARIAN BERDASARKAN 'NAME' ---
        // Pengecekan 'name' di sini dihilangkan karena tidak unik dan bisa menyebabkan penimpaan data yang salah.

        // Jika tidak ada kunci pencarian yang valid (employee_id/email), lewati baris
        if (empty($searchKeys)) {
            return null;
        }

        // 2. Panggil firstOrNew untuk mencari atau membuat instance baru
        $user = User::firstOrNew($searchKeys);

        // 3. Siapkan Data Dasar yang Akan Disisipkan atau Diperbarui
        $dataToUpdate = [
            'name'                => $row['name'],
            'email'               => $email, // Gunakan email yang sudah dinormalisasi
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

    /**
     * @param string|null $value
     * @return string|null
     * Memastikan tanggal disimpan dalam format yyyy-mm-dd
     */
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

    /**
     * Metode Pembantu 2: Menerjemahkan nilai gender (Misal: Male/Female ke L/P)
     */
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

    // Metode rules() Anda
    public function rules(): array
    {
        return [
            'name' => ['nullable'],
            'email' => ['nullable', 'email'],
            'gender' => ['nullable'],
            // ... (lanjutkan aturan validasi lainnya)
        ];
    }
}

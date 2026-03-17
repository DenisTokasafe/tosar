<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait WithSearchPelapor
{
    /**
     * Properti yang dibutuhkan oleh Trait
     * Variabel ini akan otomatis tersedia di Class yang menggunakan Trait ini.
     */
    public $searchPelapor = '';
    public $pelapors = [];
    public $showPelaporDropdown = false;
    public $manualPelaporMode = false;
    public $manualPelaporName;

    /**
     * Livewire Magic Method: Dijalankan otomatis saat komponen dimuat (mount).
     * Ini akan mengisi data pelapor secara otomatis jika user sudah login.
     */
    public function mountWithSearchPelapor()
    {
        if (Auth::check()) {
            // Mengisi ID Pelapor ke properti parent (jika ada)
            if (property_exists($this, 'pelapor_id')) {
                $this->pelapor_id = Auth::id();
            }
            // Mengisi Nama ke input search
            $this->searchPelapor = Auth::user()->name;
        }
    }

    /**
     * Menangani pencarian pelapor saat user mengetik.
     */
    public function updatedSearchPelapor()
    {
        // Reset ID dan mode manual jika user mulai mengetik ulang
        if (property_exists($this, 'pelapor_id')) {
            $this->pelapor_id = null;
        }
        $this->manualPelaporMode = false;
        $this->manualPelaporName = null;

        if (strlen($this->searchPelapor) > 1) {
            $this->pelapors = User::where('name', 'like', '%' . $this->searchPelapor . '%')
                ->orderBy('name')
                ->limit(50)
                ->get();

            $this->showPelaporDropdown = true;
        } else {
            $this->pelapors = [];
            $this->showPelaporDropdown = false;
        }
    }

    /**
     * Menangani pemilihan pelapor dari dropdown hasil pencarian.
     */
    public function selectPelapor($id, $name)
    {
        if (property_exists($this, 'pelapor_id')) {
            $this->pelapor_id = $id;
        }
        $this->searchPelapor = $name;
        $this->showPelaporDropdown = false;
        $this->manualPelaporMode = false;

        // Picu validasi jika diperlukan
        if (method_exists($this, 'validateOnly')) {
            $this->validateOnly('pelapor_id');
        }
    }

    /**
     * Mengaktifkan mode manual jika nama tidak ditemukan di database.
     */
    public function enableManualPelapor()
    {
        $this->manualPelaporMode = true;
        $this->manualPelaporName = $this->searchPelapor;
        $this->showPelaporDropdown = false;

        if (property_exists($this, 'pelapor_id')) {
            $this->pelapor_id = null;
        }

        $this->dispatch('alert', [
            'text' => "Nama '" . $this->manualPelaporName . "' ditambahkan secara manual!",
            'duration' => 5000,
            'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
        ]);
    }

    /**
     * Memastikan pelapor_id tetap null jika user mengedit nama manual.
     */
    public function updatedManualPelaporName($value)
    {
        if (property_exists($this, 'pelapor_id')) {
            $this->pelapor_id = null;
        }
    }
}

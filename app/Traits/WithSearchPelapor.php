<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait WithSearchPelapor
{
    // Pelapor
    public $searchPelapor = '';
    public $pelapors = [];
    public $showPelaporDropdown = false;
    public $manualPelaporMode = false;
    public $manualPelaporName;

    public function mountWithWithSearchPelapor()
    {
        if (Auth::check()) {
            // Mengakses $this->pelapor_id yang didefinisikan di Parent
            if (property_exists($this, 'pelapor_id')) {
                $this->pelapor_id = Auth::id();
            }
            $this->searchPelapor = Auth::user()->name;
        }
    }
    public function updatedSearchPelapor()
    {
        // Hindari reset total jika hanya ingin mengosongkan ID tapi tetap mau mencari
        $this->pelapor_id = null;
        $this->manualPelaporMode = false;
        $this->manualPelaporName = null;

        if (strlen($this->searchPelapor) > 1) {
            $this->pelapors = User::where('name', 'like', '%' . $this->searchPelapor . '%')
                ->orderBy('name')
                ->limit(50)
                ->get();

            $this->showPelaporDropdown = true;

            // Dispatch event untuk memberitahu Alpine agar re-calculate posisi dropdown
        } else {
            $this->pelapors = [];
            $this->showPelaporDropdown = false;
        }
    }
    public function selectPelapor($id, $name)
    {
        $this->pelapor_id = $id;
        $this->searchPelapor = $name;
        $this->showPelaporDropdown = false;
        $this->manualPelaporMode = false;
    }
    public function enableManualPelapor()
    {
        $this->manualPelaporMode = true;
        $this->manualPelaporName = $this->searchPelapor; // isi default sama dengan isi search
        $this->showPelaporDropdown = false;
        $this->pelapor_id = null;
        $this->dispatch(
            'alert',
            [
                'text' => "nama sudah di tambahkan!!!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
            ]
        );
    }
    public function updatedManualPelaporName($value)
    {
        $this->pelapor_id = null;
    }
}

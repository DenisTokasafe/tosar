<?php

namespace App\Livewire\Wpi;

use App\Models\User;
use Livewire\Component;
use App\Models\Location;
use App\Models\WpiReport;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\WpiFinding;
use App\Helpers\FileHelper;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $reportId;
    public $report_date, $report_time, $location, $dept_cont;
    public $inspectors = [['name' => '', 'id_number' => '']];
    public $findings = [];

    // Properti Pencarian Umum
    public $locations = [];
    public $show_location = false;
    public $searchLocation = '';
    public $search = '';
    public $departments = [];
    public $showDropdown = false;
    public $searchContractor = '';
    public $contractors = [];
    public $showContractorDropdown = false;
    public $deptCont = 'department';

    // Properti Pencarian Petugas (Independen per Baris)
    public $pelaporsAct = [];
    public $searchPetugas = [];
    public $showDropdownPetugas = [];
    public $manualActPelaporMode = false;
    public $manualActPelaporName = '';

    public function mount($id = null)
    {
        if ($id) {
            $this->loadData($id);
        } else {

            $this->addFinding();
        }
    }

    /**
     * Logika Pencarian Petugas Inspeksi (Multi-row)
     */
    public function updatedSearchPetugas($value, $key)
    {
        // Ambil index dari key, misal "searchPetugas.0" -> index = 0
        $index = explode('.', $key)[0];

        if (strlen($value) > 1) {
            $this->pelaporsAct = User::where('name', 'like', '%' . $value . '%')
                ->orderBy('name')
                ->limit(20)
                ->get();
            $this->showDropdownPetugas[$index] = true;
        } else {
            $this->showDropdownPetugas[$index] = false;
        }
    }

    public function selectActPelapor($id, $name)
    {
        // Cari index mana yang dropdown-nya sedang terbuka
        $index = collect($this->showDropdownPetugas)->search(true);
        if ($index !== false) {
            // 1. Simpan data ke array inspectors sesuai barisnya
            $this->inspectors[$index]['name'] = $name;
            $this->inspectors[$index]['id_number'] = $id;

            // 2. Update search input agar sinkron di UI
            $this->searchPetugas[$index] = $name;

            // 3. Tutup dropdown untuk baris tersebut
            $this->showDropdownPetugas[$index] = false;
        }
    }

    public function addInspector()
    {
        if (count($this->inspectors) < 6) {
            $this->inspectors[] = ['name' => '', 'id_number' => ''];
        }
    }

    public function removeInspector($index)
    {
        unset($this->inspectors[$index]);
        unset($this->searchPetugas[$index]);
        unset($this->showDropdownPetugas[$index]);

        $this->inspectors = array_values($this->inspectors);
        $this->searchPetugas = array_values($this->searchPetugas);
        $this->showDropdownPetugas = array_values($this->showDropdownPetugas);
    }

    /**
     * Logika Lokasi, Department, dan Contractor
     */
    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')->limit(10)->get();
            $this->show_location = true;
        } else {
            $this->show_location = false;
        }
    }

    public function selectLocation($id, $name)
    {
        $this->location = $id;
        $this->searchLocation = $name;
        $this->show_location = false;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->departments = Department::where('department_name', 'like', '%' . $this->search . '%')
                ->orderBy('department_name')->limit(10)->get();
            $this->showDropdown = true;
        } else {
            $this->showDropdown = false;
        }
    }

    public function selectDepartment($id, $name)
    {
        $this->reset('searchContractor');
        $this->search = $name;
        $this->dept_cont = $name;
        $this->showDropdown = false;
    }

    /**
     * Logika Findings dan File Upload
     */
    public function addFinding()
    {
        $this->findings[] = [
            'ohs_risk' => 'L',
            'description' => '',
            'prevention_action' => '',
            'pic_responsible' => '',
            'due_date' => '',
            'photos' => [],
            'photos_prevention' => [],
            'new_photos' => [],
            'new_photos_prevention' => [],
        ];
    }

    public function updatedFindings($value, $key)
    {
        if (str_ends_with($key, '.new_photos')) {
            $this->validateOnly($key, [
                'findings.*.new_photos.*' => 'image|max:2048',
            ]);
        }
        if (str_ends_with($key, '.new_photos_prevention')) {
            $this->validateOnly($key, [
                'findings.*.new_photos_prevention.*' => 'image|max:2048',
            ]);
        }
    }
    public function removeFinding($index)
    {
        // 1. Cek apakah baris ini sudah ada di database (untuk mode Edit)
        // Jika ada, kita mungkin perlu menghapus file fisiknya dari storage
        if (isset($this->findings[$index]['id'])) {
            $finding = WpiFinding::find($this->findings[$index]['id']);
            if ($finding && $finding->photos) {
                foreach ($finding->photos as $path) {
                    // Gunakan helper untuk hapus file agar storage tidak penuh
                    FileHelper::deleteFile($path);
                }
            }
            if ($finding && $finding->photos_prevention) {
                foreach ($finding->photos_prevention as $path) {
                    // Gunakan helper untuk hapus file agar storage tidak penuh
                    FileHelper::deleteFile($path);
                }
            }
        }

        // 2. Hapus baris dari array findings
        unset($this->findings[$index]);

        // 3. Reset index array agar tetap berurutan (0, 1, 2...)
        // Penting agar wire:key tidak error
        $this->findings = array_values($this->findings);

        // 4. Opsional: Pastikan minimal selalu ada 1 baris
        if (empty($this->findings)) {
            $this->addFinding();
        }
    }
    public function save()
    {
        $this->validate([
            'report_date' => 'required|date',
            'location' => 'required',
            'findings.*.description' => 'required',
            // Inspectors Validation (Array)
            'inspectors'          => 'required|array|min:1',
            'inspectors.*.name'   => 'required|string|min:3',
            'inspectors.*.id_number' => 'required',
        ],[
            'inspectors.required' => 'Minimal harus ada 1 petugas inspeksi',
            'inspectors.*.name.required' => 'Nama petugas inspeksi wajib diisi',
        ]);

        $report = WpiReport::updateOrCreate(
            ['id' => $this->reportId],
            [
                'report_date' => $this->report_date,
                'report_time' => $this->report_time,
                'location'    => $this->location,
                'department'  => $this->dept_cont,
                'inspectors'  => $this->inspectors,
            ]
        );

        if ($this->reportId) {
            $report->findings()->delete();
        }

        foreach ($this->findings as $finding) {
            $photoPaths = $finding['photos'] ?? [];
            $photoPrevention = $finding['photos_prevention'] ?? [];

            if (!empty($finding['new_photos'])) {
                foreach ($finding['new_photos'] as $photo) {
                    $photoPaths[] = FileHelper::compressAndStore($photo, 'wpi-photos', 800, 75);
                }
            }
            if (!empty($finding['new_photos_prevention'])) {
                foreach ($finding['new_photos_prevention'] as $photo) {
                    $photoPrevention[] = FileHelper::compressAndStore($photo, 'wpi-photos-prevention', 800, 75);
                }
            }

            $report->findings()->create([
                'ohs_risk' => $finding['ohs_risk'],
                'description' => $finding['description'],
                'prevention_action' => $finding['prevention_action'],
                'pic_responsible' => $finding['pic_responsible'],
                'due_date' => $finding['due_date'],
                'photos' => $photoPaths,
                'photos_prevention' => $photoPrevention,
            ]);
        }

        $this->dispatch('alert', [
            'text' => $this->reportId ? 'Data berhasil diperbarui' : 'Data berhasil disimpan',
            'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
        ]);

        return redirect()->to('/wpi-list');
    }

    public function render()
    {
        return view('livewire.wpi.index');
    }
}

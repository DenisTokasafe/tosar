<?php

namespace App\Livewire\Wpi;

use App\Models\User;
use Livewire\Component;
use App\Models\Location;
use App\Models\WpiReport;
use App\Models\Contractor;
use App\Models\Department;
use App\Helpers\FileHelper;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $reportId; // Jika ada ID, berarti mode EDIT
    public $report_date, $report_time, $location, $dept_cont;
    public $inspectors = [['name' => '', 'id_number' => '']];
    public $findings = [];
    public $locations = [];
    public $show_location = false;
    public $searchLocation = '';
    public $search = '';
    public $departments = [];
    public $showDropdown = false;
    public $searchContractor = '';
    public $contractors = [];
    public $showContractorDropdown = false;
    public $deptCont = 'department'; // default departemen
    public $showPelaporDropdown = false;
    public $pelaporsAct = [];
    public $showActPelaporDropdown = false;
        public $manualActPelaporMode = false;
    public $manualActPelaporName = '';
    public $searchActResponsibility = '';
    public $action_responsible_id = null;
    public function mount($id = null)
    {
        if ($id) {
            $this->loadData($id);
        } else {
            $this->report_date = now()->format('Y-m-d');
            $this->addFinding();
        }
    }
    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->departments = Department::where('department_name', 'like', '%' . $this->search . '%')
                ->orderBy('department_name')
                ->limit(10)
                ->get();
            $this->showDropdown = true;
        } else {
            $this->departments = [];
            $this->showDropdown = false;
        }
    }
    public function updatedSearchActResponsibility()
    {
        $this->reset('manualActPelaporName');
        $this->manualActPelaporMode = false;
        if (strlen($this->searchActResponsibility) > 1) {
            $this->pelaporsAct = User::where('name', 'like', '%' . $this->searchActResponsibility . '%')
                ->orderBy('name')
                ->limit(50)
                ->get();
            $this->showActPelaporDropdown = true;
        } else {
            $this->pelaporsAct = [];
            $this->showActPelaporDropdown = false;
        }
    }
    public function selectActPelapor($id, $name)
    {
        $this->action_responsible_id = $id;
        $this->searchActResponsibility = $name;
        $this->showActPelaporDropdown = false;
        $this->manualActPelaporMode = false;
        $this->validateOnly('action_responsible_id');
    }
    public function enableManualActPelapor()
    {
        $this->manualActPelaporMode = true;
        $this->manualActPelaporName = $this->searchPelapor; // isi default sama dengan isi search
    }
    public function updatedManualActPelaporName($value)
    {
        $this->action_responsible_id = null;
    }

    public function addActPelaporManual()
    {
        $this->searchActResponsibility = $this->manualActPelaporName;
        $this->showActPelaporDropdown = false;
        $this->action_responsible_id = null;
    }

    public function selectDepartment($id, $name)
    {
        $this->reset('searchContractor');
        $this->search = $name;
        $this->dept_cont = $name;
        $this->showDropdown = false;

        // Ambil user dari erm_assignments berdasarkan department_id
    }
    public function updatedSearchContractor()
    {
        if (strlen($this->searchContractor) > 1) {
            $this->contractors = Contractor::query()
                ->where('contractor_name', 'like', '%' . $this->searchContractor . '%')
                ->orderBy('contractor_name')
                ->limit(10)
                ->get();
            $this->showContractorDropdown = true;
        } else {
            $this->contractors = [];
            $this->showContractorDropdown = true;
        }
    }
    public function selectContractor($id, $name)
    {
        $this->reset('search');
        $this->dept_cont = $name;
        $this->searchContractor = $name;
        $this->showContractorDropdown = false;
        // Ambil user dari erm_assignments berdasarkan contractor_id
    }
    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->show_location = true;
        } else {
            $this->locations = [];
            $this->show_location = false;
        }
    }
    public function selectLocation($id, $name)
    {
        $this->location = $id;
        $this->searchLocation = $name;
        $this->show_location = false;
    }
    public function loadData($id)
    {
        $report = WpiReport::with('findings')->findOrFail($id);
        $this->reportId = $report->id;
        $this->report_date = $report->report_date->format('Y-m-d');
        $this->report_time = $report->report_time;
        $this->location = $report->location;
        $this->dept_cont = $report->department;
        $this->inspectors = $report->inspectors;

        $this->findings = $report->findings->toArray();
    }

    public function addFinding()
    {
        $this->findings[] = [
            'ohs_risk' => 'L',
            'description' => '',
            'prevention_action' => '',
            'pic_responsible' => '',
            'due_date' => '',
            'new_photos' => [] // Temporary storage untuk upload
        ];
    }
    public function updatedFindings($value, $key)
    {
        // Cek jika yang diupdate adalah field new_photos
        // Format key: findings.0.new_photos
        if (str_ends_with($key, '.new_photos')) {
            // Validasi real-time agar temporary URL terbentuk
            $this->validateOnly($key, [
                'findings.*.new_photos.*' => 'image|max:2048',
            ]);
        }
    }
    public function removeInspector($index)
    {
        // Hapus elemen berdasarkan index
        unset($this->inspectors[$index]);

        // Re-index array agar urutan nomor (1, 2, 3) di Blade tetap konsisten
        $this->inspectors = array_values($this->inspectors);
    }

    public function addInspector()
    {
        // Maksimal 6 sesuai form fisik, atau biarkan dinamis
        if (count($this->inspectors) < 6) {
            $this->inspectors[] = ['name' => '', 'id_number' => ''];
        }
    }
    public function removeFinding($index)
    {
        // Jika sedang mode EDIT dan finding sudah ada di database,
        // Anda mungkin ingin menghapus fotonya terlebih dahulu (Opsional)
        if (isset($this->findings[$index]['id'])) {
            $finding = \App\Models\WpiFinding::find($this->findings[$index]['id']);
            if ($finding && $finding->photos) {
                foreach ($finding->photos as $path) {
                    FileHelper::deleteFile($path);
                }
            }
        }

        unset($this->findings[$index]);
        $this->findings = array_values($this->findings);

        // Pastikan minimal ada 1 baris temuan jika semua dihapus
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
            'findings.*.new_photos.*' => 'nullable|image|max:2048', // Validasi foto maks 2MB
        ]);

        // 1. Simpan Header
        $report = WpiReport::updateOrCreate(
            ['id' => $this->reportId],
            [
                'report_date' => $this->report_date,
                'report_time' => $this->report_time,
                'location'    => $this->location,
                'department'  => $this->department,
                'inspectors'  => $this->inspectors,
            ]
        );

        // 2. Simpan Findings (Detail)
        if ($this->reportId) {
            $report->findings()->delete(); // Reset untuk update mudah
        }
        foreach ($this->findings as $finding) {
            $photoPaths = [];

            // Handle Upload Foto menggunakan Helper
            if (!empty($finding['new_photos'])) {
                foreach ($finding['new_photos'] as $photo) {
                    // Menggunakan helper sesuai gambar yang Anda kirim
                    // Parameter: ($file, $folder, $width, $quality)
                    $path = FileHelper::compressAndStore(
                        $photo,
                        'wpi-photos',
                        800, // lebar otomatis resize ke 800px
                        75   // kualitas kompresi 75%
                    );

                    $photoPaths[] = $path;
                }
            }
            $report->findings()->create([
                'ohs_risk' => $finding['ohs_risk'],
                'description' => $finding['description'],
                'prevention_action' => $finding['prevention_action'],
                'pic_responsible' => $finding['pic_responsible'],
                'due_date' => $finding['due_date'],
                'photos' => $photoPaths,
            ]);
        }

        $messages = $this->reportId ? 'Data berhasil di updated' : 'Data berhasil disimpan';

        $this->dispatch('alert', [
            'text'            => $messages,
            'duration'        => 5000,
            'destination'     => '/contact',
            'newWindow'       => true,
            'close'           => true,
            'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
        ]);
        $this->reset('findings');
    }

    public function delete($id)
    {
        WpiReport::find($id)->delete();
        $this->dispatch(
            'alert',
            [
                'text' => "Data berhasil di hapus!!!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "linear-gradient(to right, #ff3333, #ff6666)",
            ]
        );
    }
    public function render()
    {
        return view('livewire.wpi.index');
    }
}

<?php

namespace App\Livewire\Incident;

use App\Models\BodyPart;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\EventSubType;
use App\Models\EventType;
use App\Models\IncidentReport;
use App\Models\Likelihood;
use App\Models\RiskAssessment;
use App\Models\RiskAssessmentMatrix;
use App\Models\RiskConsequence;
use App\Models\RiskMatrixCell;
use App\Models\UnsafeAct;
use App\Models\UnsafeCondition;
use App\Models\User;
use App\Traits\WithDeptContSelection;
use App\Traits\WithSearchLocation;
use App\Traits\WithSearchPelapor;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Update extends Component
{
    use WithFileUploads, WithPagination, WithDeptContSelection, WithSearchLocation, WithSearchPelapor;
    // Di dalam class IncidentEdit extends Component
    // --- IDENTITAS & STATE ---
    public $incidentId;
    public $report_number; // Untuk ditampilkan di header
    public $currentStep = 1;

    // --- PART 1: DATA DASAR & LOKASI ---
    public $event_type_id;
    public $event_sub_type_id;
    public $date_time;
    public $location_id;
    public $location_specific;


    // --- PART 1: DEPT & PIC ---
    public $deptCont = 'dept'; // Default selector
    public $department_id;
    public $contractor_id;

    public $penanggungJawab; // User ID untuk PIC
    // --- PART 1: PELAPOR (ADVANCED SEARCH) ---
    public $pelapor_id;

    // --- PART 1: RISK MATRIX ---
    public $consequence_id;
    public $likelihood_id;
    public $RiskAssessment = null; // Menyimpan objek RiskMatrixCell yang terpilih
    public $selectedLikelihoodId, $selectedConsequenceId;
    public $risk_consequence;

    // --- PART 1: NARASI & IMPACT ---
    public $description;
    public $emergency_action;
    public $isInjury = false; // Toggle status Cedera Manusia vs Kerusakan Alat
    public $selectedBodyPartCategory;
    public $selectedBodyPart;
    public $damage_detail;

    // --- KOLEKSI DATA (FOR DROPDOWNS) ---
    // Properti ini biasanya diisi di mount() atau menggunakan Computed Properties
    public $locations = [];
    public $departments = [];
    public $contractors = [];
    public $penanggungJawabOptions = [];
    public $consequences = [];  // Untuk header table matrix
    public $likelihoods = [];   // Untuk row table matrix
    public $penerimaan_komentar_ktt_id;
    public $penerimaan_komentar_ktt;
    /**
     * Computed Property untuk Sub-Tipe Insiden
     * Otomatis update saat event_type_id berubah
     */
    #[Computed]
    public function eventSubTypes()
    {
        return EventSubType::where('event_type_id', $this->event_type_id)->get();
    }

    /**
     * Cek apakah tipe insiden yang dipilih memiliki sub-tipe
     */
    #[Computed]
    public function hasSubTypes()
    {
        return $this->eventSubTypes->isNotEmpty();
    }

    /**
     * Koleksi kategori bagian tubuh
     */
    #[Computed]
    public function existingCategory()
    {
        return BodyPart::select('category')->distinct()->get();
    }

    /**
     * Detail bagian tubuh berdasarkan kategori yang dipilih
     */
    #[Computed]
    public function detailsBodyPart()
    {
        return BodyPart::where('category', $this->selectedBodyPartCategory)
            ->select('*', DB::raw("name as display_name"))
            ->get();
    }
    #[Computed]
    public function isInjury()
    {
        if (!$this->event_type_id) {
            return false;
        }

        $type = EventType::find($this->event_type_id);

        // Menggunakan str_contains atau strtolower untuk keamanan ekstra
        return $type && str_contains(strtolower($type->event_type_name), 'injury');
    }

    /**
     * Logic pencarian pelapor (Simple search)
     */
    #[Computed]
    public function pelapors()
    {
        if (strlen($this->searchPelapor) < 2) return [];

        return User::where('name', 'like', '%' . $this->searchPelapor . '%')
            ->limit(5)
            ->get();
    }
    public function mount($id)
    {
        $this->likelihoods = Likelihood::orderByDesc('level')->get();
        $this->consequences = RiskConsequence::orderBy('level')->get();
        $this->incidentId = $id;
        $report = IncidentReport::with(['risk', 'impact'])->findOrFail($id);

        // --- DATA DASAR ---
        $this->event_type_id = $report->event_type_id;
        $this->event_sub_type_id = $report->event_sub_type_id;
        $this->date_time = $report->date_time->format('Y-m-d\TH:i');
        if ($report->location_id) {
            $this->location_id = $report->location_id;
            $this->searchLocation = $report->location?->name; // Asumsi relasi 'reporter'
        }

        $this->location_specific = $report->location_specific;

        if ($report->department_id) {
            $this->department_id = $report->department_id;
            $this->deptCont = 'department'; // Jika Anda pakai toggle selector

            // Ambil nama untuk ditampilkan di input search
            $this->search = $report->department?->department_name;

            // Load awal opsi penanggung jawab (Reuse logic dari Trait)
            $this->loadInitialPenanggungJawab('department', $report->department_id);
        }
        // 2. Cek apakah ini laporan Contractor
        elseif ($report->contractor_id) {
            $this->contractor_id = $report->contractor_id;
            $this->deptCont = 'contractor';

            // Ambil nama untuk ditampilkan di input search
            $this->searchContractor = $report->contractor?->contractor_name;

            // Load awal opsi penanggung jawab
            $this->loadInitialPenanggungJawab('contractor', $report->contractor_id);
        }
        if ($this->department_id) {
            $this->deptCont = 'dept';
        } else {
            $this->deptCont = 'cont';
        }
        $this->penanggungJawab = $report->penanggungJawab;
        $this->description = $report->description;
        $this->emergency_action = $report->emergency_action;

        // --- PELAPOR ---
        if ($report->pelapor_id) {
            $this->pelapor_id = $report->pelapor_id;
            $this->searchPelapor = $report->reporter?->name; // Asumsi relasi 'reporter'
        } else {
            $this->manualPelaporMode = true;
            $this->manualPelaporName = $report->manual_pelapor;
        }

        // --- RISK MATRIX ---
        if ($report->risk) {
            $this->consequence_id = $report->risk->consequence_id;
            $this->likelihood_id = $report->risk->likelihood_id;
            // Trigger update UI Risk Assessment
            $this->updatedLikelihoodId($this->likelihood_id);
            $this->updatedConsequenceId($this->consequence_id);
        }

        // --- IMPACT (Injury vs Damage) ---
        $this->isInjury = $report->impact->is_injury;
        if ($this->isInjury) {
            $this->selectedBodyPart = $report->impact->body_part_id;
            // Ambil kategori berdasarkan part yang terpilih
            $bodyPart = BodyPart::find($this->selectedBodyPart);
            $this->selectedBodyPartCategory = $bodyPart?->category;
        } else {
            $this->damage_detail = $report->impact->damage_detail;
        }
    }

    /**
     * Memeriksa apakah ada error validasi di Part/Step tertentu
     */
    public function isFieldInStep($step, $errors)
    {
        $fieldsPerStep = [
            1 => [
                'event_type_id',
                'event_sub_type_id',
                'date_time',
                'location_id',
                'location_specific',
                'department_id',
                'penanggungJawab',
                'description',
                'emergency_action',
                'consequence_id',
                'likelihood_id'
            ],
            2 => ['directly_involved', 'witnesses'], // Contoh untuk Part 2
            // ... tambahkan pemetaan field untuk part 3 sampai 9 di sini
        ];

        if (!isset($fieldsPerStep[$step])) return false;

        // Ambil semua key error (misal: 'event_type_id', 'directly_involved.0.name')
        $errorKeys = array_keys($errors);

        foreach ($errorKeys as $key) {
            // Cek apakah key error ada di dalam daftar field step ini
            // Menggunakan Str::is untuk menangani error array seperti 'directly_involved.*'
            foreach ($fieldsPerStep[$step] as $field) {
                if (\Illuminate\Support\Str::is($field . '*', $key)) {
                    return true;
                }
            }
        }

        return false;
    }
    public function goToStep($step)
    {
        // Di mode Edit, kita izinkan lompat tanpa validasi step sebelumnya
        $this->currentStep = $step;

        // Opsional: Scroll ke atas agar user tahu konten sudah berubah
        $this->dispatch('scroll-to-top');
    }
    // Di dalam class Update extends Component

    /**
     * Hook yang dipanggil otomatis saat $likelihood_id berubah
     */

    public function edit($likelihoodId, $consequenceId)
    {
        $this->likelihood_id = $likelihoodId;
        $this->consequence_id = $consequenceId;

        $this->selectedLikelihoodId = $likelihoodId;
        $this->selectedConsequenceId = $consequenceId;

        $this->loadRiskAssessment();
    }

    public function updatedConsequenceId()
    {
        $this->loadRiskAssessment();
    }

    public function updatedLikelihoodId()
    {
        $this->loadRiskAssessment();
    }
    protected function loadRiskAssessment(): void
    {
        if (!$this->likelihood_id || !$this->consequence_id) {
            $this->RiskAssessment = null;
            return;
        }

        $cell = RiskMatrixCell::where('likelihood_id', $this->likelihood_id)
            ->where('risk_consequence_id', $this->consequence_id)
            ->first();

        if (!$cell) {
            $this->RiskAssessment = null;
            return;
        }

        $matrix = RiskAssessmentMatrix::where('risk_matrix_cell_id', $cell->id)->first();

        $this->RiskAssessment = $matrix
            ? RiskAssessment::find($matrix->risk_assessment_id)
            : null;
    }
    public function render()
    {
        return view('livewire.incident.update', [
            'Department'   => Department::all(),
            'Contractors'  => Contractor::all(),
            'likelihoodss' => Likelihood::orderByDesc('level')->get(),
            'consequencess' => RiskConsequence::orderBy('level')->get(),
            'eventTypes' => EventType::onlyIncidents()->get(),
            'eventSubTypes' => EventSubType::where('event_type_id', $this->event_type_id)->get(),
            'ktas' => UnsafeCondition::latest()->get(),
            'ttas' => UnsafeAct::latest()->get(),
            'detailsBodyPart' => BodyPart::searchCategory($this->selectedBodyPartCategory)->orderBy('name')->get()
        ]);
    }
}

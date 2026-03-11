<?php

namespace App\Livewire\Incident;

use App\Helpers\FileHelper;
use App\Models\BodyPart;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\ErmAssignment;
use App\Models\EventSubType;
use App\Models\EventType;
use App\Models\Likelihood;
use App\Models\Location;
use App\Models\RiskAssessment;
use App\Models\RiskAssessmentMatrix;
use App\Models\RiskConsequence;
use App\Models\RiskMatrixCell;
use App\Models\UnsafeAct;
use App\Models\UnsafeCondition;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Create extends Component
{
    use WithFileUploads, WithPagination;
    public $event_type_id, $likelihoods = [], $consequences = [],
        $event_sub_type_id,
        $location_id,
        $location_spesific,
        $documentation,
        $visual_evidence, $visual_evidence_path,
        $supporting_documents, $supporting_documents_path,
        $date_time;

    #[Url]
    public $description;
    public $keyWord = 'kta';
    public $locations = [];
    public $searchKorban = [];
    public $show_employee_dropdown = [];
    public $searchLocation = '';
    public $show_location = false;
    #[Url(as: 'step')]
    public $currentStep = 1;
    public $totalSteps = 3;
    public $contractor_id, $department_id, $likelihood_id, $consequence_id;
    public $selectedLikelihoodId, $selectedConsequenceId;
    public $RiskAssessment;

    public $risk_consequence;


    public $selectedBodyPartCategory;
    // deptContractor
    public $search = '';
    public $departments = [];
    public $showDropdown = false;
    public $searchContractor = '';
    public $contractors = [];
    public $showContractorDropdown = false;
    public $penanggungJawabOptions = [];
    public $deptCont = 'department'; // default ke department
    // Pelapor
    public $pelapor_id, $searchPelapor = '';
    public $pelapors = [];
    public $showPelaporDropdown = false;
    public $manualPelaporMode = false;
    public $manualPelaporName;
    // Involved Personnel
    public $involved_personnel_id, $searchName, $involved_personnel_name;
    public $involved_personnel_options = [];
    public $showinvolvedPersonnelDropdown = false;
    public $involvedPersonnelManualMode = false;

    public $involved_personnel = []; // Array utama untuk menampung banyak korban
    public $selected_personnel = [];
    public $showBodyPart = false;
    public $body_part_id;
    public $body_part_name;
    public $corrective_actions = [];
    public $directly_involved = [];
    public function mount()
    {
        if (Auth::check()) {
            $this->pelapor_id = Auth::id();
            $this->searchPelapor = Auth::user()->name;
        }
        if (session()->has('incident_data')) {
            $this->fill(session('incident_data'));
        }
        $this->likelihoods = Likelihood::orderByDesc('level')->get();
        $this->consequences = RiskConsequence::orderBy('level')->get();

        if (empty($this->directly_involved)) {
            $this->addDirectlyInvolvedRow();
        }
    }


    protected $rules = [
        'event_type_id' => 'required|exists:event_types,id',
        'event_sub_type_id' => 'required|exists:event_sub_types,id',
        'description' => 'required|string',
        'location' => 'required|string',
        'date_time' => 'required|date',
        'pelapor_id' => 'required|exists:users,id',
        'manualPelaporName' => 'nullable|string|max:255',
        // ... rules lainnya

    ];

    // Search Location
    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(80)
                ->get();
            $this->show_location = true;
        } else {
            $this->locations = [];
            $this->show_location = false;
        }
    }
    public function selectLocation($id, $name)
    {
        $this->location_id = $id;
        $this->searchLocation = $name;
        $this->show_location = false;
    }

    public function updatedVisualEvidence()
    {
        $this->validate([
            'visual_evidence' => [
                'required',
                'max:10240', // Maksimal 10MB
                'mimes:jpg,jpeg,png,webp,avif,heic'
            ],
        ]);

        // Hapus file lama jika user mengganti gambar sebelum submit
        if ($this->visual_evidence_path) {
            FileHelper::deleteFile($this->visual_evidence_path);
        }

        // Langsung kompres dan simpan path-nya
        $this->visual_evidence_path = FileHelper::compressAndStore($this->visual_evidence, 'incident/visual_evidence/documentation');
    }
    public function updatedSupportingDocuments()
    {
        $this->validate([
            'supporting_documents' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,txt',
                'max:10240', // Maksimal 10MB total
            ],
        ]);

        // Hapus file lama jika ada
        if ($this->supporting_documents_path) {
            FileHelper::deleteFile($this->supporting_documents_path);
        }

        // Simpan file
        $this->supporting_documents_path = FileHelper::compressAndStore(
            $this->supporting_documents,
            'incident/supporting_documents/documentation'
        );
    }

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
    public function getExistingCategoryProperty()
    {
        // Definisikan urutan kategori secara manual
        $order = "'Kepala', 'Tubuh Atas', 'Batang Tubuh', 'Tubuh Bawah', 'Lainnya'";

        return BodyPart::query()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderByRaw("FIELD(category, $order)")
            ->get();
    }
    public function getHasSubTypesProperty()
    {
        if (!$this->event_type_id) {
            return false;
        }

        // Cek apakah ada anak (sub-tipe) untuk tipe yang dipilih
        return EventSubType::where('event_type_id', $this->event_type_id)->exists();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->departments = Department::where('department_name', 'like', '%' . $this->search . '%')
                ->orderBy('department_name')
                ->limit(80)
                ->get();
            $this->showDropdown = true;
        } else {
            $this->departments = [];
            $this->showDropdown = false;
        }
    }
    public function selectDepartment($id, $name)
    {
        $this->reset('searchContractor', 'contractor_id');
        $this->department_id = $id;
        $this->search = $name;
        $this->showDropdown = false;

        // Ambil user dari erm_assignments berdasarkan department_id
        $this->penanggungJawabOptions = ErmAssignment::where('department_id', $id)
            ->with('user:id,name')   // pastikan relasi user() ada di model
            ->get()
            ->pluck('user')
            ->filter()
            ->toArray();
    }
    public function updatedSearchContractor()
    {
        if (strlen($this->searchContractor) > 1) {
            $this->contractors = Contractor::query()
                ->where('contractor_name', 'like', '%' . $this->searchContractor . '%')
                ->orderBy('contractor_name')
                ->limit(80)
                ->get();
            $this->showContractorDropdown = true;
        } else {
            $this->contractors = [];
            $this->showContractorDropdown = false;
        }
    }
    public function selectContractor($id, $name)
    {
        $this->reset('search', 'department_id');
        $this->contractor_id = $id;
        $this->searchContractor = $name;
        $this->showContractorDropdown = false;
        // Ambil user dari erm_assignments berdasarkan contractor_id
        $this->penanggungJawabOptions = ErmAssignment::where('contractor_id', $id)
            ->with('user:id,name')
            ->get()
            ->pluck('user')
            ->filter()
            ->toArray();
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
      #[Computed]
    public function keterlibatanOptions()
    {
        return [
            'saksi'         => 'Saksi',
            'korban_cedera' => 'Korban Cedera',
            'kontraktor'    => 'Kontraktor',
            'operator'      => 'Operator',
            'lainnya'       => 'Lainnya',
        ];
    }
    public function render()
    {
        return view('livewire.incident.create', [
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
    // Involved Personnel

    public function addDirectlyInvolvedRow()
    {
        $this->directly_involved[] = [
            'employee_id'      => null,
            'employee_name'    => '',
            'employee_nik'     => '',
            'dept_cont'        => '',
            'jabatan'          => '',
            'roster'           => '',
            'sift'             => '',
            'keterlibatan'     => '',
            'pengalaman_kerja' => '',
        ];

        // Inisialisasi state pencarian untuk baris baru
        $newIndex = count($this->directly_involved) - 1;
        $this->searchKorban[$newIndex] = '';
        $this->show_employee_dropdown[$newIndex] = false;
    }

    /**
     * Menghapus baris berdasarkan index
     */
    public function removeDirectlyInvolvedRow($index)
    {
        unset($this->directly_involved[$index]);
        unset($this->searchKorban[$index]);
        unset($this->show_employee_dropdown[$index]);

        // Re-index array agar index tetap berurutan (mencegah error di foreach)
        $this->directly_involved = array_values($this->directly_involved);
        $this->searchKorban = array_values($this->searchKorban);
        $this->show_employee_dropdown = array_values($this->show_employee_dropdown);
    }

    /**
     * Lifecycle hook: Terpicu otomatis saat searchKorban diupdate
     */
    public function updatedSearchKorban($value, $index)
    {
        // Ambil hanya angka index dari string "searchKorban.0"
        $realIndex = explode('.', $index)[0];

        if (strlen($value) < 2) {
            $this->involved_personnel_options = [];
            $this->show_employee_dropdown[$realIndex] = false;
            return;
        }

        // Cari data karyawan (Sesuaikan kolom database Anda)
        $this->involved_personnel_options = User::where('name', 'like', '%' . $value . '%')
            ->limit(5)
            ->get();

        $this->show_employee_dropdown[$realIndex] = true;
    }

    /**
     * Fungsi saat nama diklik dari dropdown
     */
    public function selectInvolvedPersonnel($id, $name)
    {
        // Cari baris mana yang sedang aktif mencari
        // Kita bisa melacak index melalui loop atau state
        foreach ($this->show_employee_dropdown as $index => $isVisible) {
            if ($isVisible) {
                $employee = User::find($id);

                if ($employee) {
                    // Isi data baris tersebut secara otomatis
                    $this->directly_involved[$index]['employee_id']   = $employee->id;
                    $this->directly_involved[$index]['employee_name'] = $employee->name;
                    $this->directly_involved[$index]['employee_nik']  = $employee->employee_id;
                    $this->directly_involved[$index]['dept_cont']     = $employee->department_name;

                    // Reset pencarian
                    $this->searchKorban[$index] = $employee->name;
                    $this->show_employee_dropdown[$index] = false;
                }
                break;
            }
        }
    }

}

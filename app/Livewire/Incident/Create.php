<?php

namespace App\Livewire\Incident;

use App\Models\BodyPart;
use App\Models\Contractor;
use App\Models\Department;
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
use Livewire\Attributes\Url;
use Livewire\Component;

class Create extends Component
{
    public $event_type_id, $pelapor_id, $searchPelapor, $likelihoods = [], $consequences = [],
        $event_sub_type_id,
        $location_id,
        $location_spesific,
        $documentation,
        $documentation_description,
        $date_time;

    #[Url]
    public $description;
    public $keyWord = 'kta';
    public $locations = [];
    public $searchLocation = '';
    public $show_location = false;
    #[Url(as: 'step')]
    public $currentStep = 1;
    public $totalSteps = 3;
    public $contractor_id, $department_id, $likelihood_id, $consequence_id;
    public $selectedLikelihoodId, $selectedConsequenceId;
    public $RiskAssessment;

    public $risk_consequence;
    // Pelapor
    public $pelapors = [];
    public $showPelaporDropdown = false;
    public $manualPelaporMode = false;
    public $manualPelaporName;
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
    public function nextStep()
    {
        // Simpan ke session
        session(['incident_data' => $this->all()]);
        $this->currentStep++;
    }
    public function previousStep()
    {
        $this->currentStep--;
    }
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
        return BodyPart::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category', 'asc')
            ->pluck('category');
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
}

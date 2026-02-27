<?php

namespace App\Livewire\Incident;

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
use Livewire\Component;

class Create extends Component
{
    public $event_type_id,$pelapor_id, $searchPelapor,$likelihoods, $consequences,
        $event_sub_type_id,
        $description,
        $location_id,
        $location_spesific,
        $documentation,
        $documentation_description,
        $date_time;
    public $keyWord = 'kta';
    public $locations = [];
    public $searchLocation = '';
    public $show_location = false;
    public $currentStep = 1;
    public $totalSteps = 3;
    public $contractor_id,$department_id,$likelihood_id, $consequence_id;
    public $selectedLikelihoodId, $selectedConsequenceId;
    public $RiskAssessment;
        public function mount()
    {
        if (Auth::check()) {
            $this->pelapor_id = Auth::id();
            $this->searchPelapor = Auth::user()->name;
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
    ];
    public function nextStep()
    {
        $this->validate($this->rules()[$this->currentStep]);
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
}

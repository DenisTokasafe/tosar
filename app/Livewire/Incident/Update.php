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
    public $why_analysis = [];
    public $whyCount = 1;

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

    public function rules()
    {
        $rules = [
            // PART 1
            'event_type_id' => 'required|exists:event_types,id',
            'event_sub_type_id' => 'required|exists:event_sub_types,id',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'location_specific' => 'required_with:location_id|string',
            'date_time' => 'required|date',
            'pelapor_id' => 'required_without:manualPelaporName',



            // Mutual Exclusion Dept/Contractor
            'department_id' => 'nullable|required_without:contractor_id|exists:departments,id',
            'contractor_id' => 'nullable|required_without:department_id|exists:contractors,id',

            'deptCont' => 'required',
            'likelihood_id' => 'required',
            'consequence_id' => 'required',
            'emergency_action' => 'required',
            'penanggungJawab' => 'required',
            // LOGIKA KONDISIONAL BERDASARKAN isInjury
            'selectedBodyPartCategory' => $this->isInjury ? 'required' : 'nullable',
            'selectedBodyPart' => $this->isInjury ? 'required' : 'nullable',
            'damage_detail' => !$this->isInjury ? 'required|string' : 'nullable',
            // // Part 2
            // // PART 2: Pihak Terlibat Langsung
            // 'directly_involved' => 'required|array|min:1',
            // 'directly_involved.*.employee_name' => 'required|string',
            // 'directly_involved.*.employee_nik'  => 'required',
            // 'directly_involved.*.dept_cont'     => 'required',
            // 'directly_involved.*.jabatan'       => 'required',
            // 'directly_involved.*.roster'        => 'required',
            // 'directly_involved.*.sift'          => 'required',
            // 'directly_involved.*.keterlibatan'  => 'required',
            // 'directly_involved.*.pengalaman_kerja' => 'required|numeric',
            // // PART 3: Tim Investigasi
            // 'pemimpin' => 'required|array|min:1',
            // 'pemimpin.*.user_id' => 'required',
            // 'pemimpin.*.dept'    => 'required|string',
            // 'pemimpin.*.jabatan' => 'required|string',

            // 'facilitator' => 'required|array|min:1',
            // 'facilitator.*.user_id' => 'required',
            // 'facilitator.*.dept'    => 'required|string',
            // 'facilitator.*.jabatan' => 'required|string',

            // 'anggota' => 'required|array|min:1',
            // 'anggota.*.user_id' => 'required',
            // 'anggota.*.dept'    => 'required|string',
            // 'anggota.*.jabatan' => 'required|string',
            // // PART 4: PEEPO (Analisis Faktor)
            // 'peepo.orang.temuan'      => 'required|string|min:3',
            // 'peepo.orang.deskripsi'   => 'required|string|min:5',

            // 'peepo.peralatan.temuan'    => 'required|string|min:3',
            // 'peepo.peralatan.deskripsi' => 'required|string|min:5',

            // 'peepo.lingkungan.temuan'   => 'required|string|min:3',
            // 'peepo.lingkungan.deskripsi' => 'required|string|min:5',

            // 'peepo.prosedur.temuan'     => 'required|string|min:3',
            // 'peepo.prosedur.deskripsi'  => 'required|string|min:5',

            // 'peepo.organisasi.temuan'   => 'required|string|min:3',
            // 'peepo.organisasi.deskripsi' => 'required|string|min:5',
            // // PART 5: Timeline & Why Analysis
            // 'why_analysis' => 'required|array',
            // // Part 6
            // // Validasi Kondisi Tidak Aman
            // 'unsafe_conditions.*.item' => 'required',
            // 'unsafe_conditions.*.description' => 'required|string|min:5',

            // // Validasi Perilaku Tidak Aman
            // 'unsafe_acts.*.item' => 'required',
            // 'unsafe_acts.*.description' => 'required|string|min:5',

            // // Validasi Faktor Pribadi
            // 'personal_factors.*.item' => 'required',
            // 'personal_factors.*.description' => 'required|string|min:5',

            // // Validasi Faktor Pekerjaan
            // 'job_factors.*.item' => 'required',
            // 'job_factors.*.description' => 'required|string|min:5',

            // // Validasi Kelemahan Sistem Kontrol
            // 'control_system_factors.*.item' => 'required',
            // 'control_system_factors.*.description' => 'required|string|min:5',
            // // Part 7
            // 'visual_evidence' => 'required|array|min:1',

            // // Validasi tiap file di dalam array (Ukuran dan Tipe)
            // 'visual_evidence.*' => 'image|max:2048', // Maks 2MB per foto

            // 'supporting_documents' => 'required|array|min:1',
            // 'supporting_documents.*' => 'mimes:pdf,doc,docx|max:5120',
            // // Validasi Tabel Tindakan Perbaikan (Array Dinamis)
            // 'corrective_actions.*.action_description' => 'required|string|min:10',
            // 'corrective_actions.*.control_hierarchy' => 'required|in:Eliminasi,Substitusi,Engineering,Administrasi,APD',
            // 'corrective_actions.*.pic_user_id'         => 'required|exists:users,id', // Ganti 'name' jadi 'pic_user_id'
            // 'corrective_actions.*.due_date' => 'required|date|after_or_equal:date_time',
            // 'corrective_actions.*.actual_completion_date' => [
            //     'nullable',
            //     'date',
            //     // 'index' akan otomatis dipetakan oleh Laravel/Livewire untuk baris yang sama
            //     'after_or_equal:corrective_actions.*.due_date'
            // ],
            // // Part 8
            // 'key_learning' => 'required|string|min:10',
            // // Part 9
            // 'penerimaan_komentar_contractor_id' => 'required|exists:users,id',
            // 'penerimaan_komentar_internal_id'   => 'required|exists:users,id',
            // 'penerimaan_komentar_ohs_id'        => 'required|exists:users,id',
            // 'penerimaan_komentar_contractor'    => 'required|min:11',
            // 'penerimaan_komentar_internal'      => 'required|min:11',
            // 'penerimaan_komentar_ohs'           => 'required|min:11',


        ];

        // // Tambahkan Logika KTT di sini agar terbaca secara global
        // if (in_array((int)$this->consequence_id, [3, 4, 5])) {
        //     $rules['penerimaan_komentar_ktt_id'] = 'required|exists:users,id';
        //     $rules['penerimaan_komentar_ktt']    = 'required|min:11';
        // }


        // // PERBAIKAN DI SINI:
        // // Gunakan $rules, bukan $attributes.
        // // Dan pastikan key-nya sesuai dengan data binding Anda.
        // foreach (range(1, $this->whyCount) as $i) {
        //     $rules["why_analysis.why{$i}"] = 'required|string|min:3';
        // }

        return $rules;
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
        // Ambil data pelapor dari database laporan
        $this->loadInitialPelapor(
            $report->pelapor_id,
            $report->manual_pelapor_name // Sesuaikan dengan nama kolom di tabel Anda
        );

        // --- RISK MATRIX ---
        if ($report->risk) {
            $this->consequence_id = $report->risk->consequence_id;
            $this->likelihood_id = $report->risk->likelihood_id;

            // Set visual state untuk tabel matrix di blade
            $this->selectedLikelihoodId = $this->likelihood_id;
            $this->selectedConsequenceId = $this->consequence_id;

            // Cukup panggil fungsi load sekali
            $this->loadRiskAssessment();
        }

        $impact = $report->impact;
        $this->isInjury = $impact?->is_injury ?? false;

        if ($this->isInjury) {
            $this->selectedBodyPart = $impact?->body_part_id;
            // Gunakan find untuk mendapatkan kategori agar select category otomatis terpilih (selected)
            $bodyPart = BodyPart::find($this->selectedBodyPart);
            $this->selectedBodyPartCategory = $bodyPart?->category;

            // Pastikan damage_detail kosong jika ini adalah cidera
            $this->damage_detail = null;
        } else {
            $this->damage_detail = $impact?->damage_detail;

            // Pastikan data cidera kosong jika ini adalah kerusakan alat/lingkungan
            $this->selectedBodyPart = null;
            $this->selectedBodyPartCategory = null;
        }
    }
    public function updatedEventTypeId($value)
    {
        // 1. Reset sub-type setiap kali tipe utama berubah
        $this->event_sub_type_id = null;

        // 2. Tentukan logic isInjury berdasarkan Tipe Insiden (Event Type)
        // Asumsi: ID atau Nama tertentu menentukan apakah ini cidera manusia
        // Anda bisa menyesuaikan ID di bawah sesuai database SENTRY Anda
        $eventType = EventType::find($value);

        if ($eventType) {
            // Contoh logic: Jika nama tipe mengandung kata 'Cidera' atau 'Injury'
            if (
                str_contains(strtolower($eventType->event_type_name), 'injury') ||
                str_contains(strtolower($eventType->event_type_name), 'cidera')
            ) {
                $this->isInjury = true;
                $this->damage_detail = null; // Reset detail kerusakan alat
            } else {
                $this->isInjury = false;
                $this->selectedBodyPart = null; // Reset data cidera
                $this->selectedBodyPartCategory = null;
            }
        }
    }
    public function updatedSelectedBodyPartCategory()
    {
        // Reset detail bagian tubuh jika kategorinya diganti
        $this->selectedBodyPart = null;
    }
    public function getHasSubTypesProperty()
    {
        if (!$this->event_type_id) {
            return false;
        }

        // Cek apakah ada anak (sub-tipe) untuk tipe yang dipilih
        return EventSubType::where('event_type_id', $this->event_type_id)->exists();
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

        // Ambil data severity dari tabel risk_matrix
        $cell = RiskMatrixCell::where('likelihood_id', $this->likelihood_id)
            ->where('risk_consequence_id', $this->consequence_id)
            ->first();

        if (!$cell || !$cell->severity) {
            $this->RiskAssessment = null;
            return;
        }

        // Cocokkan kolom 'severity' di Matrix dengan 'name' di RiskAssessment
        $this->RiskAssessment = RiskAssessment::where('name', $cell->severity)->first();
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
    public function nextStep()
    {
        // Validasi hanya field yang ada di step saat ini (misal Step 1)
        $this->validateOnlyStep($this->currentStep);

        if ($this->currentStep < 9) {
            $this->currentStep++;
            // Kirim event ke browser untuk scroll ke atas jika perlu
            $this->dispatch('scroll-to-top');
        }
    }
    /**
     * Berpindah antar step/part secara langsung
     */
    public function goToStep($step)
    {
        // Opsional: Validasi minimal sebelum pindah (jika diperlukan)
        // $this->validateOnlyStep($this->currentStep);

        $this->currentStep = $step;

        // Kirim event ke browser untuk scroll ke atas agar user tidak bingung
        // saat konten di bawah berubah
        $this->dispatch('scroll-to-top');
    }

    protected function validateOnlyStep($step)
    {
        // 1. Ambil semua rules utama dari method rules() sebagai referensi
        $allRules = $this->rules();

        // 2. Definisikan Base Rules kondisional (untuk digunakan di array_merge)
        $isInjuryRules = $this->isInjury
            ? [
                'selectedBodyPartCategory' => $allRules['selectedBodyPartCategory'],
                'selectedBodyPart' => $allRules['selectedBodyPart']
            ]
            : [
                'damage_detail' => $allRules['damage_detail']
            ];

        $kttRules = in_array((int)$this->consequence_id, [3, 4, 5])
            ? [
                'penerimaan_komentar_ktt_id' => $allRules['penerimaan_komentar_ktt_id'],
                'penerimaan_komentar_ktt' => $allRules['penerimaan_komentar_ktt']
            ]
            : [];

        // 3. Pemetaan Rules per Step
        $stepRules = [
            1 => array_merge([
                'event_type_id'      => $allRules['event_type_id'],
                'event_sub_type_id'  => $allRules['event_sub_type_id'],
                'description'        => $allRules['description'],
                'location_id'        => $allRules['location_id'],
                'location_specific'  => $allRules['location_specific'],
                'date_time'          => $allRules['date_time'],
                'pelapor_id'         => $allRules['pelapor_id'],
                'department_id'      => $allRules['department_id'],
                'contractor_id'      => $allRules['contractor_id'],
                'deptCont'           => $allRules['deptCont'],
                'likelihood_id'      => $allRules['likelihood_id'],
                'consequence_id'     => $allRules['consequence_id'],
                'emergency_action'   => $allRules['emergency_action'],
                'penanggungJawab'    => $allRules['penanggungJawab'],
            ], $isInjuryRules),

            // 2 => [
            //     'directly_involved' => $allRules['directly_involved'],
            //     'directly_involved.*.employee_name' => $allRules['directly_involved.*.employee_name'],
            //     'directly_involved.*.employee_nik'  => $allRules['directly_involved.*.employee_nik'],
            //     'directly_involved.*.dept_cont'     => $allRules['directly_involved.*.dept_cont'],
            //     'directly_involved.*.jabatan'       => $allRules['directly_involved.*.jabatan'],
            //     'directly_involved.*.roster'        => $allRules['directly_involved.*.roster'],
            //     'directly_involved.*.sift'          => $allRules['directly_involved.*.sift'],
            //     'directly_involved.*.keterlibatan'  => $allRules['directly_involved.*.keterlibatan'],
            //     'directly_involved.*.pengalaman_kerja' => $allRules['directly_involved.*.pengalaman_kerja'],
            // ],

            // 3 => [
            //     'pemimpin' => $allRules['pemimpin'],
            //     'pemimpin.*.user_id' => $allRules['pemimpin.*.user_id'],
            //     'pemimpin.*.dept'    => $allRules['pemimpin.*.dept'],
            //     'pemimpin.*.jabatan' => $allRules['pemimpin.*.jabatan'],
            //     'facilitator' => $allRules['facilitator'],
            //     'facilitator.*.user_id' => $allRules['facilitator.*.user_id'],
            //     'anggota' => $allRules['anggota'],
            // ],

            // 4 => [
            //     'peepo.orang.temuan'       => $allRules['peepo.orang.temuan'],
            //     'peepo.orang.deskripsi'    => $allRules['peepo.orang.deskripsi'],
            //     'peepo.peralatan.temuan'   => $allRules['peepo.peralatan.temuan'],
            //     'peepo.peralatan.deskripsi' => $allRules['peepo.peralatan.deskripsi'],
            //     'peepo.lingkungan.temuan'  => $allRules['peepo.lingkungan.temuan'],
            //     'peepo.prosedur.temuan'    => $allRules['peepo.prosedur.temuan'],
            //     'peepo.organisasi.temuan'  => $allRules['peepo.organisasi.temuan'],
            // ],

            // 5 => $this->getWhyAnalysisRules(),

            // 6 => [
            //     'unsafe_conditions.*.item' => $allRules['unsafe_conditions.*.item'],
            //     'unsafe_conditions.*.description' => $allRules['unsafe_conditions.*.description'],
            //     'unsafe_acts.*.item' => $allRules['unsafe_acts.*.item'],
            //     'personal_factors.*.item' => $allRules['personal_factors.*.item'],
            //     'job_factors.*.item' => $allRules['job_factors.*.item'],
            //     'control_system_factors.*.item' => $allRules['control_system_factors.*.item'],
            // ],

            // 7 => [
            //     'visual_evidence' => $allRules['visual_evidence'],
            //     'visual_evidence.*' => $allRules['visual_evidence.*'],
            //     'supporting_documents' => $allRules['supporting_documents'],
            //     'corrective_actions.*.action_description' => $allRules['corrective_actions.*.action_description'],
            //     'corrective_actions.*.pic_user_id'         => $allRules['corrective_actions.*.pic_user_id'],
            //     'corrective_actions.*.due_date'           => $allRules['corrective_actions.*.due_date'],
            // ],

            // 8 => [
            //     'key_learning' => $allRules['key_learning'],
            // ],

            // 9 => array_merge([
            //     'penerimaan_komentar_contractor_id' => $allRules['penerimaan_komentar_contractor_id'],
            //     'penerimaan_komentar_internal_id'   => $allRules['penerimaan_komentar_internal_id'],
            //     'penerimaan_komentar_ohs_id'        => $allRules['penerimaan_komentar_ohs_id'],
            //     'penerimaan_komentar_contractor'    => $allRules['penerimaan_komentar_contractor'],
            //     'penerimaan_komentar_internal'      => $allRules['penerimaan_komentar_internal'],
            //     'penerimaan_komentar_ohs'           => $allRules['penerimaan_komentar_ohs'],
            // ], $kttRules),
        ];

        // 4. Jalankan Validasi berdasarkan step saat ini
        if (isset($stepRules[$step])) {
            return $this->validate($stepRules[$step]);
        }
    }

    /**
     * Helper untuk dynamic why analysis rules
     */
    protected function getWhyAnalysisRules()
    {
        $rules = ['why_analysis' => 'required|array'];
        foreach (range(1, $this->whyCount ?? 5) as $i) {
            $rules["why_analysis.why{$i}"] = 'required|string|min:3';
        }
        return $rules;
    }

    public function isFieldInStep($step, $errorFields)
    {
        // Ambil semua key field yang sedang error
        $fields = array_keys($errorFields);

        foreach ($fields as $field) {
            switch ($step) {
                case 1:
                    $step1Fields = [
                        'event_type_id',
                        'event_sub_type_id',
                        'date_time',
                        'location_id',
                        'location_specific',
                        'department_id',
                        'contractor_id',
                        'penanggungJawab',
                        'pelapor_id',
                        'manualPelaporName',
                        'consequence_id',
                        'likelihood_id',
                        'description',
                        'emergency_action',
                        'selectedBodyPartCategory',
                        'selectedBodyPart',
                        'damage_detail'
                    ];
                    if (in_array($field, $step1Fields)) return true;
                    break;

                    // case 2:
                    //     if (str_starts_with($field, 'directly_involved')) return true;
                    //     break;

                    // case 3:
                    //     if (collect(['pemimpin', 'facilitator', 'anggota'])->some(fn($p) => str_starts_with($field, $p))) return true;
                    //     break;

                    // case 4:
                    //     if (str_starts_with($field, 'peepo')) return true;
                    //     break;

                    // case 5:
                    //     if (str_starts_with($field, 'timelines')) return true;
                    //     break;

                    // case 6:
                    //     if (collect(['unsafe', 'personal_factors', 'job_factors', 'control_system_factors'])->some(fn($p) => str_starts_with($field, $p))) return true;
                    //     break;

                    // case 7:
                    //     if (collect(['visual_evidence', 'supporting_documents', 'corrective_actions'])->some(fn($p) => str_starts_with($field, $p))) return true;
                    //     break;

                    // case 8:
                    //     if ($field === 'key_learning') return true;
                    //     break;

                    // case 9:
                    //     if (str_starts_with($field, 'penerimaan_komentar')) return true;
                    //     break;
            }
        }

        return false;
    }
}

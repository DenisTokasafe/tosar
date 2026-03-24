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

    public $directly_involved = []; // Menampung data baris personel
    public $searchKorban = [];      // Menampung input pencarian per baris
    public $show_employee_dropdown = []; // Status dropdown per baris
    public $involved_personnel_options = []; // Hasil pencarian DB

    // Data Tim Investigasi
    public $pemimpin = [];
    public $facilitator = [];
    public $anggota = [];

    // State untuk Pencarian/Dropdown
    public $searchQuery = []; // Struktur: [$index][$type] => 'string'
    public $showDropdownPartisipan = [];
    public $activeType = null;
    public $activeIndex = null;
    public $options = []; // Hasil pencarian User
    public $peepo = [];
    public $peepoFactors = [
        'orang' => 'Orang',
        'peralatan' => 'Peralatan',
        'lingkungan' => 'Lingkungan',
        'prosedur' => 'Prosedur',
        'organisasi' => 'Organisasi'
    ];

    public $unsafe_conditions = [];
    public $unsafe_acts = [];
    public $personal_factors = [];
    public $job_factors = [];
    public $control_system_factors = [];

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
            // Part 2
            // PART 2: Pihak Terlibat Langsung
            'directly_involved' => 'required|array|min:1',
            'directly_involved.*.employee_name' => 'required|string',
            'directly_involved.*.employee_nik'  => 'required',
            'directly_involved.*.dept_cont'     => 'required',
            'directly_involved.*.jabatan'       => 'required',
            'directly_involved.*.roster'        => 'required',
            'directly_involved.*.sift'          => 'required',
            'directly_involved.*.keterlibatan'  => 'required',
            'directly_involved.*.pengalaman_kerja' => 'required|numeric',
            // PART 3: Tim Investigasi
            'pemimpin' => 'required|array|min:1',
            'pemimpin.*.user_id' => 'required',
            'pemimpin.*.dept'    => 'required|string',
            'pemimpin.*.jabatan' => 'required|string',

            'facilitator' => 'required|array|min:1',
            'facilitator.*.user_id' => 'required',
            'facilitator.*.dept'    => 'required|string',
            'facilitator.*.jabatan' => 'required|string',

            'anggota' => 'required|array|min:1',
            'anggota.*.user_id' => 'required',
            'anggota.*.dept'    => 'required|string',
            'anggota.*.jabatan' => 'required|string',
            // PART 4: PEEPO (Analisis Faktor)
            'peepo.orang.temuan'      => 'required|string|min:3',
            'peepo.orang.deskripsi'   => 'required|string|min:5',

            'peepo.peralatan.temuan'    => 'required|string|min:3',
            'peepo.peralatan.deskripsi' => 'required|string|min:5',

            'peepo.lingkungan.temuan'   => 'required|string|min:3',
            'peepo.lingkungan.deskripsi' => 'required|string|min:5',

            'peepo.prosedur.temuan'     => 'required|string|min:3',
            'peepo.prosedur.deskripsi'  => 'required|string|min:5',

            'peepo.organisasi.temuan'   => 'required|string|min:3',
            'peepo.organisasi.deskripsi' => 'required|string|min:5',
            // PART 5: Timeline & Why Analysis
            'why_analysis' => 'required|array',
            // Part 6
            // Validasi Kondisi Tidak Aman
            'unsafe_conditions.*.item' => 'required',
            'unsafe_conditions.*.description' => 'required|string|min:5',

            // Validasi Perilaku Tidak Aman
            'unsafe_acts.*.item' => 'required',
            'unsafe_acts.*.description' => 'required|string|min:5',

            // Validasi Faktor Pribadi
            'personal_factors.*.item' => 'required',
            'personal_factors.*.description' => 'required|string|min:5',

            // Validasi Faktor Pekerjaan
            'job_factors.*.item' => 'required',
            'job_factors.*.description' => 'required|string|min:5',

            // Validasi Kelemahan Sistem Kontrol
            'control_system_factors.*.item' => 'required',
            'control_system_factors.*.description' => 'required|string|min:5',
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
        foreach (range(1, $this->whyCount) as $i) {
            $rules["why_analysis.why{$i}"] = 'required|string|min:3';
        }

        return $rules;
    }
    public function updated($propertyName)
    {
        // 1. Logika Bisnis: Update otomatis Status/Progress
        // Jalankan ini DI AWAL agar perubahan properti langsung tercermin di class
        if (str_contains($propertyName, 'corrective_actions')) {
            $parts = explode('.', $propertyName);

            if (isset($parts[1]) && isset($parts[2]) && $parts[2] === 'actual_completion_date') {
                $index = $parts[1];

                if (!empty($this->corrective_actions[$index]['actual_completion_date'])) {
                    $this->corrective_actions[$index]['status'] = 'Selesai';
                    $this->corrective_actions[$index]['progress'] = 100;
                } else {
                    $this->corrective_actions[$index]['status'] = 'Belum Selesai';
                    $this->corrective_actions[$index]['progress'] = 0;
                }
                $this->dispatch('refresh-component');
            }
        }

        // 2. Simpan ke Session
        // Gunakan fungsi helper saveToSession yang sudah Anda buat agar kode tidak duplikat
        $this->saveToSession();

        // 3. Validasi Kondisional
        if ($propertyName === 'event_type_id') {
            $this->validateOnly('selectedBodyPartCategory');
            $this->validateOnly('selectedBodyPart');
            $this->validateOnly('damage_detail');
        }

        // 4. Validasi Standar (Real-time feedback)
        $this->validateOnly($propertyName);
    }
    protected function saveToSession()
    {
        $data = $this->all();
        $data['whyCount'] = $this->whyCount;
        // Hapus file dan properti yang tidak bisa diserialisasi
        unset(
            $data['visual_evidence'],
            $data['supporting_documents'],
            $data['visual_evidence_paths'],
            $data['supporting_documents_paths']
        );

        session()->put('incident_data', $data);
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

        // PART 2: Load data personel terlibat
        if ($report->involvedPersons->count() > 0) {
            foreach ($report->involvedPersons as $person) {
                $this->directly_involved[] = [
                    'id' => $person->id, // Simpan ID untuk keperluan update nanti
                    'employee_id' => $person->employee_id,
                    'employee_name' => $person->employee_name,
                    'employee_nik' => $person->employee_nik,
                    'dept_cont' => $person->dept_cont,
                    'jabatan' => $person->jabatan,
                    'roster' => $person->roster,
                    'sift' => $person->shift,
                    'keterlibatan' => $person->keterlibatan,
                    'pengalaman_kerja' => $person->pengalaman_kerja,
                ];

                // Isi search input dengan nama yang sudah ada
                $this->searchKorban[] = $person->employee_name;
            }
        } else {
            // Jika data kosong, berikan 1 baris kosong default
            $this->addDirectlyInvolvedRow();
        }

        // PART 3: Load Tim Investigasi
        $teams = $report->investigationTeams;

        foreach (['pemimpin', 'facilitator', 'anggota'] as $role) {
            // Menggunakan filter agar bisa bekerja seperti "LIKE" dan Case-Insensitive
            $filtered = $teams->filter(function ($item) use ($role) {
                // stripos mengembalikan posisi angka jika ditemukan, atau false jika tidak
                return stripos($item->role, $role) !== false;
            });

            if ($filtered->isNotEmpty()) {
                foreach ($filtered->values() as $index => $team) {
                    $this->{$role}[] = [
                        'user_id' => $team->user_id,
                        'dept'    => $team->dept,
                        'jabatan' => $team->jabatan,
                    ];
                    $this->searchQuery[$index][$role] = $team->user->name ?? '';
                }
            } else {
                $this->addRow($role);
            }

            // PART 4: Load PEEPO
            foreach ($this->peepoFactors as $key => $label) {
                // Cari berdasarkan factor_key (orang, peralatan, dll)
                $data = $report->peepoAnalyses->where('factor_key', $key)->first();

                $this->peepo[$key] = [
                    'temuan'    => $data->temuan ?? '',
                    'deskripsi' => $data->deskripsi ?? '',
                ];
            }
        }

        // Gunakan first() jika relasi mengembalikan banyak data
        $analysis = $report->timelines()->first();

        // Atau jika relasi di model IncidentReport sudah benar (HasOne), cukup:
        // A. Ambil data dari JSON SCAT (jika sudah pernah disimpan)
        $scat = $report->scat_analysis;

        if ($scat) {
            // Pastikan mapping sesuai dengan struktur di prepareArrayData
            $this->unsafe_conditions = $scat['langsung']['kondisi_tidak_aman'] ?? [];
            $this->unsafe_acts       = $scat['langsung']['perilaku_tidak_aman'] ?? [];

            $this->personal_factors  = $scat['dasar']['faktor_pribadi'] ?? [];
            $this->job_factors       = $scat['dasar']['faktor_pekerjaan'] ?? [];
            $this->control_system_factors = $scat['dasar']['sistem_kontrol'] ?? [];
        }
        // B. Proteksi Form Kosong
        $categories = [
            'unsafe_conditions',
            'unsafe_acts',
            'personal_factors',
            'job_factors',
            'control_system_factors'
        ];

        foreach ($categories as $category) {
            // Jika tidak ada data dari DB, tambahkan 1 baris kosong default
            if (empty($this->{$category})) {
                $this->addRow($category);
            }
        }
        // $analysis = $report->timeline;

        if ($analysis && is_array($analysis->analysis_steps)) {
            $this->why_analysis = $analysis->analysis_steps;
            $this->whyCount = count($this->why_analysis) ?: 1;
        } else {
            $this->why_analysis = ['why1' => ''];
            $this->whyCount = 1;
        }
    }

    public function getUnsafeConditionOptionsProperty()
    {
        return [
            '1.1.1 Tidak memadainya pengamanan atau penghalang' => '1.1.1 Tidak memadainya pengamanan atau penghalang',
            '1.1.2 Tidak memadainya atau tidak layaknya peralatan pencegah' => '1.1.2 Tidak memadainya atau tidak layaknya peralatan pencegah',
            '1.1.3 Perkakas, peralatan atau bahan(material) yang rusak' => '1.1.3 Perkakas, peralatan atau bahan(material) yang rusak',
            '1.1.4 Tempat kerja sangat terbatas' => '1.1.4 Tempat kerja sangat terbatas',
            '1.1.5 Kurang memadainya Sistem peringatan' => '1.1.5 Kurang memadainya Sistem peringatan',
            '1.1.6 Bahaya kebakaran dan ledakan' => '1.1.6 Bahaya kebakaran dan ledakan',
            '1.1.7 Housekeeping jelek/berantakan' => '1.1.7 Housekeeping jelek/berantakan',
            '1.1.8 Kebisingan' => '1.1.8 Kebisingan',
            '1.1.9 Radiasi' => '1.1.9 Radiasi',
            '1.1.10 Suhu yang ekstrim' => '1.1.10 Suhu yang ekstrim',
            '1.1.11 Kurangnya penerangan / berlebihan' => '1.1.11 Kurangnya penerangan / berlebihan',
            '1.1.12 Ventilasi' => '1.1.12 Ventilasi',
            '1.1.13 Kondisi lingkungan yang berbahaya' => '1.1.13 Kondisi lingkungan yang berbahaya',
            '1.1.14 Lainnya' => '1.1.14 Lainnya',
        ];
    }
    #[Computed]
    public function unsafeActOptions()
    {
        return [
            '1.2.1 Mengoperasikan peralatan tanpa izin' => '1.2.1 Mengoperasikan peralatan tanpa izin',
            '1.2.2 Gagal / lalai memperingatkan' => '1.2.2 Gagal / lalai memperingatkan',
            '1.2.3 Gagal / lalai mengamankan' => '1.2.3 Gagal / lalai mengamankan',
            '1.2.4 Mengoperasikan dengan kecepatan tidak sesuai' => '1.2.4 Mengoperasikan dengan kecepatan tidak sesuai',
            '1.2.5 Membuat alat pengaman tidak berfungsi' => '1.2.5 Membuat alat pengaman tidak berfungsi',
            '1.2.6 Memakai alat yang rusak' => '1.2.6 Memakai alat yang rusak',
            '1.2.7 Gagal / lalai menggunakan APD yang semestinya' => '1.2.7 Gagal / lalai menggunakan APD yang semestinya',
            '1.2.8 Pembebanan yang tidak sesuai' => '1.2.8 Pembebanan yang tidak sesuai',
            '1.2.9 Salah meletakkan / memuat' => '1.2.9 Salah meletakkan / memuat',
            '1.2.10 Pengangkatan yang tidak sesuai' => '1.2.10 Pengangkatan yang tidak sesuai',
            '1.2.11 Berada di tempat / posisi yang terlarang' => '1.2.11 Berada di tempat / posisi yang terlarang',
            '1.2.12 Memperbaiki peralatan yang bekerja / bergerak' => '1.2.12 Memperbaiki peralatan yang bekerja / bergerak',
            '1.2.13 Bercanda berlebihan' => '1.2.13 Bercanda berlebihan',
            '1.2.14 Di bawah pengaruh alkohol dan/atau obat terlarang' => '1.2.14 Di bawah pengaruh alkohol dan/atau obat terlarang',
            '1.2.15 Memakai peralatan yang bukan semestinya' => '1.2.15 Memakai peralatan yang bukan semestinya',
            '1.2.16 Gagal / lalai mengikuti prosedur' => '1.2.16 Gagal / lalai mengikuti prosedur',
            '1.2.17 Lainnya' => '1.2.17 Lainnya',
        ];
    }
    #[Computed]
    public function personalFactorOptions()
    {
        return [
            '2.1.1 Tidak memadainya kemampuan fisik / fisiologis' => '2.1.1 Tidak memadainya kemampuan fisik / fisiologis',
            '2.1.2 Keterbatasan mental / Kemampuan psikologi' => '2.1.2 Keterbatasan mental / Kemampuan psikologi',
            '2.1.3 Tekanan Fisik atau fisiologis' => '2.1.3 Tekanan Fisik atau fisiologis',
            '2.1.4 Mental atau Tekanan psikologis' => '2.1.4 Mental atau Tekanan psikologis',
            '2.1.5 Kurangnya pengetahuan' => '2.1.5 Kurangnya pengetahuan',
            '2.1.6 Kurangnya keahlian' => '2.1.6 Kurangnya keahlian',
            '2.1.7 Salah Motivasi' => '2.1.7 Salah Motivasi',
            '2.1.8 Lainnya' => '2.1.8 Lainnya',
        ];
    }

    #[Computed]
    public function jobFactorOptions()
    {
        return [
            '2.2.1 Kepemimpinan dan atau Fungsi pengawasan tidak memadai' => '2.2.1 Kepemimpinan dan atau Fungsi pengawasan tidak memadai',
            '2.2.2 Engineering yang tidak memadai' => '2.2.2 Engineering yang tidak memadai',
            '2.2.3 Pembelian yang tidak memadai' => '2.2.3 Pembelian yang tidak memadai',
            '2.2.4 Pemeliharaan yang tidak memadai' => '2.2.4 Pemeliharaan yang tidak memadai',
            '2.2.5 Alat dan peralatan yang tidak memadai' => '2.2.5 Alat dan peralatan yang tidak memadai',
            '2.2.6 Standar-standar kerja yang tidak memadai' => '2.2.6 Standar-standar kerja yang tidak memadai',
            '2.2.7 Pemakaian yang berlebihan' => '2.2.7 Pemakaian yang berlebihan',
            '2.2.8 Salah pakai atau penyalahgunaan' => '2.2.8 Salah pakai atau penyalahgunaan',
            '2.2.9 Lainnya' => '2.2.9 Lainnya',
        ];
    }

    #[Computed]
    public function controlSystemOptions()
    {
        return [
            '2.3.1 Perangkat Keras' => '2.3.1 Perangkat Keras',
            '2.3.2 Pelatihan' => '2.3.2 Pelatihan',
            '2.3.3 Organisasi' => '2.3.3 Organisasi',
            '2.3.4 Komunikasi' => '2.3.4 Komunikasi',
            '2.3.5 Sasaran tidak kompatibel' => '2.3.5 Sasaran tidak kompatibel',
            '2.3.6 Prosedur' => '2.3.6 Prosedur',
            '2.3.7 Manajemen Pemeliharaan' => '2.3.7 Manajemen Pemeliharaan',
            '2.3.8 Disain' => '2.3.8 Disain',
            '2.3.9 Manajemen Resiko' => '2.3.9 Manajemen Resiko',
            '2.3.10 Manajemen Perubahan' => '2.3.10 Manajemen Perubahan',
            '2.3.11 Manajemen Kontraktor' => '2.3.11 Manajemen Kontraktor',
            '2.3.12 Budaya Organisasi' => '2.3.12 Budaya Organisasi',
            '2.3.13 Pengaruh Peraturan' => '2.3.13 Pengaruh Peraturan',
            '2.3.14 Pembelajaran Organisasi' => '2.3.14 Pembelajaran Organisasi',
            '2.3.15 Manajemen Kendaraan' => '2.3.15 Manajemen Kendaraan',
            '2.3.16 Sistem Manajemen' => '2.3.16 Sistem Manajemen',
            '2.3.17 Lainnya' => '2.3.17 Lainnya',
        ];
    }

    #[Computed]
    public function gridClass()
    {
        return match (true) {
            $this->whyCount == 2 => 'grid-cols-2',
            $this->whyCount >= 3 => 'grid-cols-3',
            default => 'grid-cols-1',
        };
    }
    public function addWhyColumn()
    {
        $this->whyCount++;

        // Inisialisasi key baru di setiap baris timeline agar tidak error
        $this->why_analysis['why' . $this->whyCount] = '';
        $this->saveToSession();
    }
    public function removeWhyColumn()
    {
        if ($this->whyCount > 1) {
            unset($this->why_analysis['why' . $this->whyCount]);
            $this->whyCount--;
        }
        $this->saveToSession();
    }

    public function addDirectlyInvolvedRow()
    {
        $this->directly_involved[] = [
            'employee_id' => null,
            'employee_name' => '',
            'employee_nik' => '',
            'dept_cont' => '',
            'jabatan' => '',
            'roster' => '',
            'sift' => '',
            'keterlibatan' => '',
            'pengalaman_kerja' => '',
        ];

        // Inisialisasi state pembantu untuk index baru
        $newIndex = count($this->directly_involved) - 1;
        $this->searchKorban[$newIndex] = '';
        $this->show_employee_dropdown[$newIndex] = false;
        $this->saveToSession();
    }

    public function removeDirectlyInvolvedRow($index)
    {
        unset($this->directly_involved[$index]);
        unset($this->searchKorban[$index]);
        unset($this->show_employee_dropdown[$index]);

        // Reset index agar berurutan kembali (penting untuk kelancaran array PHP)
        $this->directly_involved = array_values($this->directly_involved);
        $this->searchKorban = array_values($this->searchKorban);
        $this->show_employee_dropdown = array_values($this->show_employee_dropdown);
    }


    // Fungsi pencarian otomatis saat user mengetik
    public function updatedSearchKorban($value, $index)
    {
        // Ambil index dari string "searchKorban.0"
        $idx = explode('.', $index)[0];

        if (strlen($value) >= 2) {
            // Ganti dengan logic pencarian database Anda
            $this->involved_personnel_options = User::where('name', 'like', "%{$value}%")
                ->limit(5)
                ->get();
            $this->show_employee_dropdown[$idx] = true;
        } else {
            $this->show_employee_dropdown[$idx] = false;
        }
    }
    public function selectInvolvedPersonnel($id, $name, $index)
    {
        // 1. Cari data karyawan di database
        // Sesuaikan model 'Employee' dengan model yang Anda gunakan
        $employee = User::find($id);

        if ($employee) {
            // 2. Isi data ke array directly_involved berdasarkan index-nya
            $this->directly_involved[$index]['employee_name'] = $employee->name;
            $this->directly_involved[$index]['employee_id']   = $employee->id; // Untuk tracking ID
            $this->directly_involved[$index]['employee_nik']  = $employee->employee_id;

            // Asumsi relasi department atau kolom string dept
            $this->directly_involved[$index]['dept_cont']     = $employee->department_name ?? '';
            $this->directly_involved[$index]['jabatan']       = $employee->position;

            // 3. Reset dropdown search untuk baris ini
            $this->show_employee_dropdown[$index] = false;
            $this->searchKorban[$index] = $employee->name;

            // 4. PENTING: Simpan perubahan ke session agar tidak hilang saat refresh
            session(['incident_data' => $this->all()]);

            // 5. Opsional: Trigger validasi untuk baris tersebut
            $this->validateOnly("directly_involved.$index.*");
        }
    }


    public function addRow($type)
    {
        // 1. Tentukan struktur data berdasarkan tipe
        // Tambahkan pengecekan untuk faktor pribadi, pekerjaan, dan sistem kontrol
        if (in_array($type, ['unsafe_conditions', 'unsafe_acts', 'personal_factors', 'job_factors', 'control_system_factors'])) {
            $newData = ['item' => '', 'description' => ''];
        } elseif ($type === 'timelines') {
            // Timeline dengan struktur khusus sesuai jumlah kolom "Why"
            $newData = ['kegiatan' => '', 'tanggal' => ''];
            for ($i = 1; $i <= $this->whyCount; $i++) {
                $newData["why{$i}"] = '';
            }
        } else {
            // Default untuk partisipan (pemimpin, facilitator, anggota)
            $newData = ['user_id' => null, 'nama' => '', 'jabatan' => '', 'jabatan_detail' => '', 'dept' => ''];
        }

        // 2. Masukkan ke array utama secara dinamis
        $this->{$type}[] = $newData;

        // 3. Inisialisasi state pembantu untuk pencarian User
        if (in_array($type, ['pemimpin', 'facilitator', 'anggota',])) {
            $newIndex = count($this->{$type}) - 1;
            $this->searchQuery[$newIndex][$type] = '';
            $this->showDropdownPartisipan[$newIndex] = false;
        }
        $this->saveToSession();
    }
    public function removeRow($type, $index)
    {
        // 1. Hapus data baris utama
        if (isset($this->{$type}[$index])) {
            unset($this->{$type}[$index]);
            $this->{$type} = array_values($this->{$type});
        }

        // 2. Sinkronisasi searchQuery dan Dropdown
        // Kita tidak bisa hanya array_values secara global karena akan menggeser
        // data tipe lain yang berada di indeks yang sama.

        // Cara terbaik: Hapus data indeks tersebut, lalu geser manual data di bawahnya
        // khusus untuk tipe (key) yang sedang dihapus.

        $totalRemaining = count($this->{$type});

        for ($i = $index; $i <= $totalRemaining; $i++) {
            // Geser searchQuery untuk tipe yang spesifik ini saja
            if (isset($this->searchQuery[$i + 1][$type])) {
                $this->searchQuery[$i][$type] = $this->searchQuery[$i + 1][$type];
                unset($this->searchQuery[$i + 1][$type]);
            } else {
                unset($this->searchQuery[$i][$type]);
            }

            // Geser status dropdown
            if (isset($this->showDropdownPartisipan[$i + 1])) {
                $this->showDropdownPartisipan[$i] = $this->showDropdownPartisipan[$i + 1];
            } else {
                unset($this->showDropdownPartisipan[$i]);
            }
        }

        // 3. Jika baris habis, tambahkan satu baris kosong lagi
        if (empty($this->{$type})) {
            $this->addRow($type);
        }
        $this->saveToSession();
    }
    public function updatedSearchQuery($value, $key)
    {
        // $key sekarang berisi "0.pemimpin", "1.facilitator", dst.
        $parts = explode('.', $key);
        $index = $parts[0]; // Mendapatkan angka index

        if (strlen($value) < 2) {
            $this->options = [];
            $this->showDropdownPartisipan[$index] = false;
            return;
        }

        $this->options = User::where('name', 'like', '%' . $value . '%')->limit(50)->get();

        // Buka dropdown berdasarkan index barisnya
        $this->showDropdownPartisipan[$index] = true;
    }
    public function selectUser($id, $index, $type)
    {
        $user = User::find($id);

        if ($user) {
            // 1. Set data utama
            $this->{$type}[$index]['user_id'] = $user->id;
            $this->{$type}[$index]['nama']    = $user->name;

            // 2. Set default Jabatan & Dept (Sangat membantu user agar tidak ketik manual)
            $this->{$type}[$index]['jabatan'] = $user->position ?? '';
            $this->{$type}[$index]['dept']    = $user->department_name ?? '';

            // 3. Update teks input pencarian agar sinkron dengan pilihan
            // Pastikan $this->searchQuery sudah didefinisikan sebagai array di awal
            $this->searchQuery[$index][$type] = $user->name;

            // 4. Reset state dropdown
            $this->showDropdownPartisipan[$index] = false;
            $this->options = [];

            // 5. TRIGGER VALIDASI (PENTING)
            // Menghapus pesan error merah segera setelah data terpilih
            $this->validateOnly($type . '.' . $index . '.user_id');
            $this->validateOnly($type . '.' . $index . '.dept');
            $this->validateOnly($type . '.' . $index . '.jabatan');
            // SIMPAN KE SESSION
            $this->saveToSession();
        }
    }
    public function resetSearch()
    {
        $this->searchQuery = []; // Reset ke array kosong
        $this->options = [];
        $this->showDropdownPartisipan = []; // Reset ke array kosong
        $this->activeType = '';
        $this->activeIndex = null;
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

            2 => [
                'directly_involved' => $allRules['directly_involved'],
                'directly_involved.*.employee_name' => $allRules['directly_involved.*.employee_name'],
                'directly_involved.*.employee_nik'  => $allRules['directly_involved.*.employee_nik'],
                'directly_involved.*.dept_cont'     => $allRules['directly_involved.*.dept_cont'],
                'directly_involved.*.jabatan'       => $allRules['directly_involved.*.jabatan'],
                'directly_involved.*.roster'        => $allRules['directly_involved.*.roster'],
                'directly_involved.*.sift'          => $allRules['directly_involved.*.sift'],
                'directly_involved.*.keterlibatan'  => $allRules['directly_involved.*.keterlibatan'],
                'directly_involved.*.pengalaman_kerja' => $allRules['directly_involved.*.pengalaman_kerja'],
            ],

            3 => [
                // Pemimpin Investigasi
                'pemimpin'           => $allRules['pemimpin'],
                'pemimpin.*.user_id' => $allRules['pemimpin.*.user_id'],
                'pemimpin.*.dept'    => $allRules['pemimpin.*.dept'],
                'pemimpin.*.jabatan' => $allRules['pemimpin.*.jabatan'],

                // Facilitator (KPLH)
                'facilitator'           => $allRules['facilitator'],
                'facilitator.*.user_id' => $allRules['facilitator.*.user_id'],
                'facilitator.*.dept'    => $allRules['facilitator.*.dept'],
                'facilitator.*.jabatan' => $allRules['facilitator.*.jabatan'],

                // Tim Anggota
                'anggota'           => $allRules['anggota'],
                'anggota.*.user_id' => $allRules['anggota.*.user_id'],
                'anggota.*.dept'    => $allRules['anggota.*.dept'],
                'anggota.*.jabatan' => $allRules['anggota.*.jabatan'],
            ],

            4 => [
                'peepo.orang.temuan'       => $allRules['peepo.orang.temuan'],
                'peepo.orang.deskripsi'    => $allRules['peepo.orang.deskripsi'],
                'peepo.peralatan.temuan'   => $allRules['peepo.peralatan.temuan'],
                'peepo.peralatan.deskripsi' => $allRules['peepo.peralatan.deskripsi'],
                'peepo.lingkungan.temuan'  => $allRules['peepo.lingkungan.temuan'],
                'peepo.prosedur.temuan'    => $allRules['peepo.prosedur.temuan'],
                'peepo.organisasi.temuan'  => $allRules['peepo.organisasi.temuan'],
            ],

            5 => $this->getWhyAnalysisRules(),

            6 => [
                'unsafe_conditions.*.item' => $allRules['unsafe_conditions.*.item'],
                'unsafe_conditions.*.description' => $allRules['unsafe_conditions.*.description'],
                'unsafe_acts.*.item' => $allRules['unsafe_acts.*.item'],
                'personal_factors.*.item' => $allRules['personal_factors.*.item'],
                'job_factors.*.item' => $allRules['job_factors.*.item'],
                'control_system_factors.*.item' => $allRules['control_system_factors.*.item'],
            ],

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

                case 2:
                    if (str_starts_with($field, 'directly_involved')) return true;
                    break;

                case 3:
                    if (collect(['pemimpin', 'facilitator', 'anggota'])->some(fn($p) => str_starts_with($field, $p))) return true;
                    break;

                case 4:
                    if (str_starts_with($field, 'peepo')) return true;
                    break;

                case 5:
                    if (str_starts_with($field, 'timelines')) return true;
                    break;

                case 6:
                    if (collect(['unsafe', 'personal_factors', 'job_factors', 'control_system_factors'])->some(fn($p) => str_starts_with($field, $p))) return true;
                    break;

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
    public function update()
    {
        // 1. Validasi berdasarkan Step yang sedang aktif
        $this->validateOnlyStep($this->currentStep);

        $report = IncidentReport::findOrFail($this->incidentId);

        // 2. Update data utama (Step 1: Incident Details)
        $report->update([
            // Identitas & Waktu
            'event_type_id'     => $this->event_type_id,
            'event_sub_type_id' => $this->event_sub_type_id,
            'description'       => $this->description,
            'date_time'         => $this->date_time,

            // Lokasi
            'location_id'       => $this->location_id,
            'location_specific' => $this->location_specific,

            // Pelapor (Handle Manual Mode dari Trait)
            'pelapor_id'          => $this->pelapor_id,
            'manual_pelapor_name' => $this->manualPelaporName,

            // Organisasi (Mutual Exclusion Dept/Cont)
            'department_id' => $this->deptCont === 'department' ? $this->department_id : null,
            'contractor_id' => $this->deptCont === 'contractor' ? $this->contractor_id : null,

            // Risk Assessment
            'likelihood_id'  => $this->likelihood_id,
            'consequence_id' => $this->consequence_id,

            // Tindakan & Penanggung Jawab
            'emergency_action' => $this->emergency_action,
            'penanggung_jawab' => $this->penanggungJawab, // Pastikan nama kolom di DB sesuai

            // Logika Kondisional isInjury
            'body_part_category' => $this->isInjury ? $this->selectedBodyPartCategory : null,
            'body_part'          => $this->isInjury ? $this->selectedBodyPart : null,
            'damage_detail'      => !$this->isInjury ? $this->damage_detail : null,
            // TAMBAHKAN INI UNTUK PART 6 (SCAT)
            'scat_analysis' => [
                'langsung' => [
                    'kondisi_tidak_aman' => $this->unsafe_conditions,
                    'perilaku_tidak_aman' => $this->unsafe_acts,
                ],
                'dasar' => [
                    'faktor_pribadi'   => $this->personal_factors,
                    'faktor_pekerjaan' => $this->job_factors,
                    'sistem_kontrol'   => $this->control_system_factors,
                ],
            ],
        ]);

        // 3. Update Part 2 (Involved Personnel)
        // Menggunakan metode delete-then-insert untuk sinkronisasi array
        $report->involvedPersons()->delete();

        foreach ($this->directly_involved as $person) {
            $report->involvedPersons()->create([
                'employee_id'      => $person['employee_id'] ?? null,
                'employee_name'    => $person['employee_name'],
                'employee_nik'     => $person['employee_nik'],
                'dept_cont'        => $person['dept_cont'],
                'jabatan'          => $person['jabatan'],
                'roster'           => $person['roster'],
                'sift'             => $person['sift'] ?? $person['shift'] ?? null, // Proteksi typo 'sift'
                'keterlibatan'     => $person['keterlibatan'],
                'pengalaman_kerja' => $person['pengalaman_kerja'],
            ]);
        }
        // --- STEP 3: Sinkronisasi Tim Investigasi ---
        // Hapus data tim lama
        $report->investigationTeams()->delete();

        // Loop melalui 3 kategori tim
        foreach (['pemimpin', 'facilitator', 'anggota'] as $role) {
            foreach ($this->{$role} as $member) {
                // Hanya simpan jika user_id atau jabatan tidak kosong
                if (!empty($member['user_id']) || !empty($member['jabatan'])) {
                    $report->investigationTeams()->create([
                        'user_id' => $member['user_id'],
                        'role'    => $role,
                        'dept'    => $member['dept'],
                        'jabatan' => $member['jabatan'],
                    ]);
                }
            }
        }
        // Di dalam fungsi update()
        foreach ($this->peepo as $key => $value) {
            $report->peepoAnalyses()->updateOrCreate(
                ['factor_key' => $key], // Match berdasarkan key (orang, peralatan, dll)
                [
                    'factor_name' => $this->peepoFactors[$key], // Nama lengkap untuk display
                    'temuan'      => $value['temuan'],
                    'deskripsi'   => $value['deskripsi'],
                ]
            );
        }
        $report->timelines()->updateOrCreate(
            ['incident_report_id' => $report->id],
            [
                'analysis_steps' => $this->why_analysis, // Otomatis jadi JSON oleh Eloquent
            ]
        );

        // 4. Navigasi atau Notifikasi
        if ($this->currentStep < 9) {
            $this->currentStep++;
            $this->dispatch('alert', [
                'text' => "Data Step {$this->currentStep} berhasil disimpan.",
                'type' => 'success'
            ]);
        } else {
            return redirect()->route('incident.index')
                ->with('success', 'Laporan insiden berhasil diperbarui secara keseluruhan.');
        }
    }
}

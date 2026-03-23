<?php

namespace App\Livewire\Incident;

use \App\Traits\WithDeptContSelection;
use App\Helpers\FileHelper;
use App\Models\BodyPart;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\ErmAssignment;
use App\Models\EventSubType;
use App\Models\EventType;
use App\Models\IncidentReport;
use App\Models\Likelihood;
use App\Models\Location;
use App\Models\RiskAssessment;
use App\Models\RiskAssessmentMatrix;
use App\Models\RiskConsequence;
use App\Models\RiskMatrixCell;
use App\Models\UnsafeAct;
use App\Models\UnsafeCondition;
use App\Models\User;
use App\Traits\WithSearchLocation;
use App\Traits\WithSearchPelapor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Create extends Component
{
    use WithFileUploads, WithPagination, WithDeptContSelection, WithSearchLocation, WithSearchPelapor;

    public $event_type_id, $event_sub_type_id, $description, $location_id, $location_specific;
    public $date_time, $pelapor_id, $manualPelaporName;
    public $department_id, $contractor_id, $penanggungJawab;
    public $deptCont = 'dept';
    public $keyWord = 'kta';
    public $likelihood_id, $consequence_id, $emergency_action;
    public $damage_detail, $selectedBodyPartCategory, $selectedBodyPart;

    public $likelihoods = [], $consequences = [],
        $location_spesific,
        $documentation,
        $visual_evidence_path,
        $supporting_documents_path;
    #[Url(as: 'step')]


    public $selectedLikelihoodId, $selectedConsequenceId;
    public $RiskAssessment;
    public $risk_consequence;



    public $penanggungJawabOptions = [];
    // Involved Personnel
    public $involved_personnel_id, $searchName, $involved_personnel_name;
    public $showinvolvedPersonnelDropdown = false;
    public $involvedPersonnelManualMode = false;

    public $involved_personnel = []; // Array utama untuk menampung banyak korban
    public $selected_personnel = [];
    public $showBodyPart = false;
    public $body_part_id;
    public $body_part_name;
    public $corrective_actions = [];
    public array $directly_involved = [];
    public array $searchKorban = []; // Menyimpan text pencarian per baris
    public array $show_employee_dropdown = []; // Menyimpan state open/close dropdown per baris
    public $involved_personnel_options = [];

    // State untuk menyimpan baris data
    public $pemimpin = [];
    public $facilitator = [];
    public $anggota = [];

    // State untuk search (karena searchable-select butuh model binding)
    public $searchQuery = [];
    public $searchQueryFacilitator = [];
    public $showDropdownPartisipan = [];
    public $visual_evidence_paths = []; // Ubah dari string ke array
    public $supporting_documents_paths = []; // Ubah dari string ke array
    public $options = [];

    // Penanda baris mana yang sedang aktif dicari
    public $activeType = '';
    public $activeIndex = null;

    public $peepoFactors = [
        'orang' => 'Orang',
        'peralatan' => 'Peralatan',
        'lingkungan' => 'Lingkungan',
        'prosedur' => 'Prosedur',
        'organisasi' => 'Organisasi'
    ];
    public $showPenerimaanKomentarContractorDropdown = false;
    public $showPenerimaanKomentarInternalDropdown = false;
    public $showPenerimaanKomentarOhsDropdown = false;
    public $showPenerimaanKomentarKttDropdown = false;

    // State tambahan untuk tracking fokus (opsional, sesuai Blade kamu)
    public $activeTypePenerimaan = '';
    public $activeIndexPenerimaan = null;

    // Pastikan Anda menginisialisasi array penampung data di mount
    public $peepo = [];
    public $why_analysis = [];
    public $whyCount = 1; // Default 5, bisa diubah menjadi 6, 7, dst.
    public $unsafe_conditions = [];
    public $unsafe_acts = [];
    public $personal_factors = [];
    public $job_factors = [];
    public $control_system_factors = [];

    // Data Utama

    // State untuk Searchable Select di dalam baris
    public $searchPetugas = [];         // Menampung input teks pencarian per index
    public $showDropdownPetugas = [];   // Menampung status open/close per index
    public $pelaporsAct = [];
    public $visual_evidence = [];
    public $supporting_documents = [];      // Hasil query pencarian (biasanya global atau di-filter)
    public $manualActPelaporMode = false; // Jika mode manual global atau per baris

    // Properti untuk menyimpan ID terpilih
    public $penerimaan_komentar_contractor_id;
    public $penerimaan_komentar_internal_id;
    public $penerimaan_komentar_ohs_id;
    public $penerimaan_komentar_ktt_id;

    // Properti untuk teks editor (CKEditor)
    public $penerimaan_komentar_contractor;
    public $penerimaan_komentar_internal;
    public $penerimaan_komentar_ohs;
    public $penerimaan_komentar_ktt;
    public $key_learning;

    public $searchNamePenerimaan = [
        'kontraktor' => '',
        'internal' => '',
        'ohs' => '',
        'ktt' => '',
    ];

    protected function rules()
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
            'keyWord' => 'required',
            'likelihood_id' => 'required',
            'consequence_id' => 'required',
            'emergency_action' => 'required',
            'penanggungJawab' => 'required',
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
            // Part 7
            'visual_evidence' => 'required|array|min:1',

            // Validasi tiap file di dalam array (Ukuran dan Tipe)
            'visual_evidence.*' => 'image|max:2048', // Maks 2MB per foto

            'supporting_documents' => 'required|array|min:1',
            'supporting_documents.*' => 'mimes:pdf,doc,docx|max:5120',
            // Validasi Tabel Tindakan Perbaikan (Array Dinamis)
            'corrective_actions.*.action_description' => 'required|string|min:10',
            'corrective_actions.*.control_hierarchy' => 'required|in:Eliminasi,Substitusi,Engineering,Administrasi,APD',
            'corrective_actions.*.pic_user_id'         => 'required|exists:users,id', // Ganti 'name' jadi 'pic_user_id'
            'corrective_actions.*.due_date' => 'required|date|after_or_equal:date_time',
            'corrective_actions.*.actual_completion_date' => [
                'nullable',
                'date',
                // 'index' akan otomatis dipetakan oleh Laravel/Livewire untuk baris yang sama
                'after_or_equal:corrective_actions.*.due_date'
            ],
            // Part 8
            'key_learning' => 'required|string|min:10',
            // Part 9
            'penerimaan_komentar_contractor_id' => 'required|exists:users,id',
            'penerimaan_komentar_internal_id'   => 'required|exists:users,id',
            'penerimaan_komentar_ohs_id'        => 'required|exists:users,id',
            'penerimaan_komentar_contractor'    => 'required|min:11',
            'penerimaan_komentar_internal'      => 'required|min:11',
            'penerimaan_komentar_ohs'           => 'required|min:11',

            // LOGIKA KONDISIONAL BERDASARKAN isInjury
            'selectedBodyPartCategory' => $this->isInjury ? 'required' : 'nullable',
            'selectedBodyPart' => $this->isInjury ? 'required' : 'nullable',
            'damage_detail' => !$this->isInjury ? 'required|string' : 'nullable',
        ];

        // Tambahkan Logika KTT di sini agar terbaca secara global
        if (in_array((int)$this->consequence_id, [3, 4, 5])) {
            $rules['penerimaan_komentar_ktt_id'] = 'required|exists:users,id';
            $rules['penerimaan_komentar_ktt']    = 'required|min:11';
        }

        // Tambahkan validasi dinamis berdasarkan jumlah whyCount yang sedang aktif
        foreach (range(1, $this->whyCount) as $i) {
            $attributes["why_analysis.why{$i}"] = __("Analisis Mengapa ke-$i");
        }

        return $rules;
    }
    /**
     * Mengembalikan atribut validasi yang sudah diterjemahkan.
     */
    protected function validationAttributes()
    {
        $attributes = [
            // Part 1
            'pelapor_id'        => __('Nama Pelapor'),
            'manualPelaporName' => __('Nama Pelapor Manual'),
            'event_type_id'     => __('Tipe Kejadian'),
            'event_sub_type_id' => __('Sub Tipe Kejadian'),
            'description'       => __('Deskripsi Kejadian'),
            'location_id'       => __('Lokasi Utama'),
            'location_specific' => __('Detail Lokasi Spesifik'),
            'date_time'         => __('Tanggal dan Waktu'),

            // KTA & TTA

            'keyWord'             => __('Jenis Bahaya'),

            // Organisasi
            'department_id'   => __('Departemen'),
            'contractor_id'   => __('Perusahaan Kontraktor'),
            'deptCont'        => __('Pihak Terlibat'),
            'penanggungJawab' => __('PIC / Penanggung Jawab'),

            // Risiko & Tindakan
            'likelihood_id'    => __('Kemungkinan (Likelihood)'),
            'consequence_id'   => __('Konsekuensi (Consequence)'),
            'emergency_action' => __('Tindakan Darurat'),

            // Kondisional Injury / Damage
            'selectedBodyPartCategory' => __('Kategori Bagian Tubuh'),
            'selectedBodyPart'         => __('Detail Bagian Tubuh'),
            'damage_detail'            => __('Detail Kerusakan Alat / Lingkungan'),
            // PART 2 (Dynamic Label)
            'directly_involved.*.employee_name' => __('Nama Personel'),
            'directly_involved.*.employee_nik'  => __('NIK/ID'),
            'directly_involved.*.dept_cont'     => __('Departemen/Perusahaan'),
            'directly_involved.*.jabatan'       => __('Jabatan'),
            'directly_involved.*.roster'        => __('Roster'),
            'directly_involved.*.sift'          => __('Shift'),
            'directly_involved.*.keterlibatan'  => __('Jenis Keterlibatan'),
            'directly_involved.*.pengalaman_kerja' => __('Pengalaman Kerja'),
            // Part 3
            'pemimpin.*.user_id' => __('Nama Pemimpin'),
            'pemimpin.*.dept'    => __('Departemen Pemimpin'),
            'pemimpin.*.jabatan' => __('Jabatan Pemimpin'),

            'facilitator.*.user_id' => __('Nama Facilitator'),
            'facilitator.*.dept'    => __('Departemen Facilitator'),
            'facilitator.*.jabatan' => __('Jabatan Facilitator'),

            'anggota.*.user_id' => __('Nama Anggota'),
            'anggota.*.dept'    => __('Departemen Anggota'),
            'anggota.*.jabatan' => __('Jabatan Anggota'),
            // Part 5: Atribut Dinamis untuk Why
            'why_analysis' => __('Analisis Mengapa'),
            // Part 7
            // Validasi Dokumen Pendukung (Multiple)
            'visual_evidence.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'supporting_documents.*' => 'mimes:pdf,doc,docx|max:5120',
            // Tindakan Perbaikan (Corrective Actions)
            'corrective_actions.*.action_description.required' => __('Rencana perbaikan wajib diisi.'),
            'corrective_actions.*.control_hierarchy.required'  => __('Pilih salah satu hirarki kontrol.'),
            'corrective_actions.*.name.required'               => __('PIC wajib dipilih.'),
            'corrective_actions.*.due_date.after_or_equal' => __('Tanggal tidak boleh lebih kecil dari  (:date_time).'),
            'corrective_actions.*.actual_completion_date.after_or_equal' => __('Tanggal selesai tidak boleh lebih kecil dari  (:due_date).'),

            // Part 9
            'penerimaan_komentar_contractor_id' => __('Penanggung Jawab Kontraktor'),
            'penerimaan_komentar_internal_id'   => __('Penanggung Jawab Internal'),
            'penerimaan_komentar_ohs_id'        => __('Penanggung Jawab OHS'),
            'penerimaan_komentar_contractor'    => __('Komentar Kontraktor'),
            'penerimaan_komentar_internal'      => __('Komentar Internal'),
            'penerimaan_komentar_ohs'           => __('Komentar OHS'),
            'penerimaan_komentar_ktt'           => __('Komentar KTT'),

        ];

        // Tambahkan atribut dinamis untuk PEEPO
        foreach ($this->peepoFactors as $key => $label) {
            $attributes["peepo.$key.temuan"]    = __('Temuan Faktor ') . $label;
            $attributes["peepo.$key.deskripsi"] = __('Deskripsi Faktor ') . $label;
        }

        // Loop untuk membuat label yang dinamis dan user-friendly
        foreach (['unsafe_conditions', 'unsafe_acts', 'personal_factors', 'job_factors', 'control_system_factors'] as $key) {
            foreach ($this->$key as $index => $row) {
                $rowNum = $index + 1;
                $label = str_replace('_', ' ', ucwords($key, '_'));
                $attributes["$key.$index.item"] = __("$label Baris $rowNum");
                $attributes["$key.$index.description"] = __("Deskripsi $label Baris $rowNum");
            }
        }
        return $attributes;
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

    // Komentar Standard
    public function messages()
    {
        return [
            // Pesan Standar
            'required' => __(':attribute wajib diisi.'),
            'exists'   => __('Pilihan :attribute tidak valid.'),
            'min'      => __(':attribute minimal harus :min karakter.'),
            'date'     => __('Format tanggal :attribute tidak sesuai.'),
            'after_or_equal' => __(':attribute tidak boleh tanggal lampau.'),

            // --- PART 7: DOKUMENTASI ---
            'supporting_documents.*.mimes' => __('Hanya file PDF dan Word yang diperbolehkan.'),
            'supporting_documents.*.max'   => __('Ukuran file dokumen tidak boleh lebih dari 5MB.'),

            'visual_evidence.required' => __('Bukti visual wajib dilampirkan.'),
            'visual_evidence.*.image'  => __('File harus berupa gambar (JPG, PNG, WebP).'),
            'visual_evidence.*.mimes'  => __('Format file tidak didukung. Gunakan JPG atau PNG.'),
            'visual_evidence.*.max'    => __('Ukuran foto maksimal 2MB.'),

            // --- PART 7: TINDAKAN PERBAIKAN ---
            'corrective_actions.*.action_description.required' => __('Rencana perbaikan wajib diisi.'),
            'corrective_actions.*.action_description.min'      => __('Deskripsi rencana perbaikan terlalu singkat.'),
            'corrective_actions.*.control_hierarchy.required'  => __('Pilih salah satu hirarki kontrol.'),
            'corrective_actions.*.name.required'               => __('PIC (Penanggung Jawab) wajib dipilih.'),
            'corrective_actions.*.due_date.required'           => __('Batas waktu (Due Date) wajib diisi.'),
            'corrective_actions.*.actual_completion_date.required'           => __('Batas waktu (Due Date) wajib diisi.'),
            // Part 8
            'key_learning.required' => __('Kunci pembelajaran wajib diisi sebagai bahan evaluasi.'),
            'key_learning.min' => __('Mohon berikan penjelasan kunci pembelajaran yang lebih detail (min. 10 karakter).'),

            // --- PESAN KHUSUS LOGIKA SENTRY ---

            'department_id.required_without'       => __('Silakan pilih Departemen atau Kontraktor.'),
            'contractor_id.required_without'       => __('Pilih Kontraktor atau Department terkait.'),
        ];
    }

    public $currentStep = 1; // Pastikan dimulai dari 1
    public $totalSteps = 9;

    public function validateCurrentStep()
    {
        $fields = [];

        switch ($this->currentStep) {
            case 1:
                $fields = [
                    'event_type_id',
                    'event_sub_type_id',
                    'description',
                    'location_id',
                    'location_specific',
                    'date_time',
                    'pelapor_id',

                    'department_id',
                    'contractor_id',
                    'deptCont',
                    'keyWord',
                    'likelihood_id',
                    'consequence_id',
                    'emergency_action',
                    'penanggungJawab',
                    'selectedBodyPartCategory',
                    'selectedBodyPart',
                    'damage_detail'
                ];
                break;

            case 2:
                // Tambahkan field untuk Part 2 (Saksi, korban, dll)
                $fields = [
                    'directly_involved',
                    'directly_involved.*.employee_name',
                    'directly_involved.*.employee_nik',
                    'directly_involved.*.dept_cont',
                    'directly_involved.*.jabatan',
                    'directly_involved.*.roster',
                    'directly_involved.*.sift',
                    'directly_involved.*.keterlibatan',
                    'directly_involved.*.pengalaman_kerja',
                ];
                break;
            case 3:
                $fields = [
                    'pemimpin',
                    'pemimpin.*.user_id',
                    'pemimpin.*.dept',
                    'pemimpin.*.jabatan',
                    'facilitator',
                    'facilitator.*.user_id',
                    'facilitator.*.dept',
                    'facilitator.*.jabatan',
                    'anggota',
                    'anggota.*.user_id',
                    'anggota.*.dept',
                    'anggota.*.jabatan',
                ];
                break;
            case 4:
                $fields = [];
                foreach (array_keys($this->peepoFactors) as $key) {
                    $fields[] = "peepo.$key.temuan";
                    $fields[] = "peepo.$key.deskripsi";
                }
                break;
            case 5:
                // Hapus 'timelines.*.kejadian' karena kita tidak pakai baris timeline lagi
                $fields = [];

                // Daftarkan semua kolom why yang aktif di dalam properti why_analysis
                for ($i = 1; $i <= $this->whyCount; $i++) {
                    $fields[] = "why_analysis.why{$i}";
                }
                break;
            case 6:
                $fields = [
                    'unsafe_conditions.*.item',
                    'unsafe_conditions.*.description',
                    'unsafe_acts.*.item',
                    'unsafe_acts.*.description',
                    'personal_factors.*.item',
                    'personal_factors.*.description',
                    'job_factors.*.item',
                    'job_factors.*.description',
                    'control_system_factors.*.item',
                    'control_system_factors.*.description',
                ];
                break;
            case 7:
                $fields = [
                    'visual_evidence',
                    'visual_evidence.*',
                    'supporting_documents',
                    'supporting_documents.*',

                    // Tabel Tindakan Perbaikan
                    'corrective_actions.*.action_description',
                    'corrective_actions.*.control_hierarchy',
                    // REVISI: Samakan dengan properti yang menyimpan ID User (bukan Nama)
                    'corrective_actions.*.pic_user_id',
                    'corrective_actions.*.due_date',
                    // Tambahkan ini jika Anda mewajibkan tanggal selesai diisi di Step 7
                    // 'corrective_actions.*.actual_completion_date',
                ];
                break;
            case 8:
                $fields = [
                    'key_learning'
                ];
                $this->dispatch('validate-key_learning');
                break;
            case 9:
                $fields = [
                    'penerimaan_komentar_contractor_id',
                    'penerimaan_komentar_internal_id',
                    'penerimaan_komentar_ohs_id',
                    'penerimaan_komentar_contractor',
                    'penerimaan_komentar_internal',
                    'penerimaan_komentar_ohs'
                ];
                break;
        }

        if (!empty($fields)) {
            // Pastikan rules() adalah PUBLIC
            $allRules = $this->rules();

            // Filter rules hanya untuk field yang ada di step aktif
            $stepRules = array_intersect_key($allRules, array_flip($fields));

            // Validasi dengan atribut dan pesan custom
            $this->validate($stepRules, $this->messages(), $this->validationAttributes());
        }
    }
    public function goToStep($step)
    {
        // Jika user mencoba lompat ke step di depannya, validasi dulu step sekarang
        if ($step > $this->currentStep) {
            $this->validateCurrentStep();
        }

        $this->currentStep = $step;
    }
    // Fungsi untuk pindah step
    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->dispatch('scroll-to-top');
        }
    }

    public function removeFile($property, $index)
    {
        if (isset($this->{$property}[$index])) {
            unset($this->{$property}[$index]);
            $this->{$property} = array_values($this->{$property}); // Reset index array
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

    // State untuk menampilkan dropdown

    public function mount()
    {
        // 1. Load data referensi statis (Paling aman ditaruh di atas)
        $this->likelihoods = Likelihood::orderByDesc('level')->get();
        $this->consequences = RiskConsequence::orderBy('level')->get();
        if (empty($this->why_analysis)) {
            $this->why_analysis = ['why1' => ''];
        }
        // 2. PRIORITAS UTAMA: Ambil data dari Session jika ada
        if (session()->has('incident_data')) {
            $data = session('incident_data');
            $this->fill($data);

            // Pastikan whyCount ikut terisi dari session
            $this->whyCount = $data['whyCount'] ?? 1;
        }
        // Inisialisasi jika session kosong
        $roles = ['pemimpin', 'facilitator', 'anggota'];
        foreach ($roles as $role) {
            if (empty($this->{$role})) {
                $this->addRow($role);
            }
        }
        // 3. INISIALISASI DEFAULT (Hanya jika data masih kosong / belum ada di session)

        // Inisialisasi Pihak Terlibat
        if (empty($this->directly_involved)) {
            $this->addDirectlyInvolvedRow();
        }


        // Inisialisasi Timeline & Faktor-faktor
        $categories = [
            'unsafe_conditions',
            'unsafe_acts',
            'personal_factors',
            'job_factors',
            'control_system_factors'
        ];

        foreach ($categories as $category) {
            if (empty($this->{$category})) {
                $this->addRow($category);
            }
        }

        // Inisialisasi PEEPO
        foreach ($this->peepoFactors as $key => $label) {
            if (!isset($this->peepo[$key])) {
                $this->peepo[$key] = ['temuan' => '', 'deskripsi' => ''];
            }
        }

        // Inisialisasi Tindakan Perbaikan
        if (empty($this->corrective_actions)) {
            $this->addCorrectiveRow();
        }
    }


    public function updatedDeptCont($value)
    {
        if ($value === 'department') {
            $this->contractor_id = null;
            $this->searchContractor = '';
        } else {
            $this->department_id = null;
            $this->search = '';
        }
    }




    public function updatedVisualEvidence()
    {
        try {
            // 1. Validasi setiap file di dalam array secara real-time
            $this->validateOnly('visual_evidence.*', [
                'visual_evidence.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ], [
                'visual_evidence.*.image' => 'File harus berupa gambar.',
                'visual_evidence.*.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
                'visual_evidence.*.max'   => 'Ukuran foto maksimal 2MB.',
            ]);

            // 2. Jika validasi lolos, bersihkan file lama dari storage (jika ada)
            if (!empty($this->visual_evidence_paths)) {
                foreach ($this->visual_evidence_paths as $oldPath) {
                    FileHelper::deleteFile($oldPath);
                }
            }

            // 3. Reset array path untuk data baru
            $this->visual_evidence_paths = [];

            // 4. Looping dan simpan (Compress) hanya jika file valid
            foreach ($this->visual_evidence as $file) {
                $this->visual_evidence_paths[] = FileHelper::compressAndStore(
                    $file,
                    'incident/visual_evidence/documentation'
                );
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 5. AUTO-CLEAR: Jika ada file yang salah format (seperti PDF),
            // kita reset array-nya agar preview file yang salah hilang dari UI.
            $this->visual_evidence = [];

            // Lempar kembali error agar muncul di komponen x-form.upload
            throw $e;
        }
    }

    public function updatedSupportingDocuments()
    {
        try {
            // 1. Validasi setiap dokumen (PDF, DOC, DOCX, XLS, XLSX)
            $this->validateOnly('supporting_documents.*', [
                'supporting_documents.*' => 'file|mimes:pdf,doc,docx|max:5120', // Max 5MB per file
            ], [
                'supporting_documents.*.file'  => 'Input harus berupa file valid.',
                'supporting_documents.*.mimes' => 'Format file harus PDF, Word',
                'supporting_documents.*.max'   => 'Ukuran file dokumen maksimal 5MB.',
            ]);

            // 2. Bersihkan file lama dari storage jika validasi berhasil
            if (!empty($this->supporting_documents_paths)) {
                foreach ($this->supporting_documents_paths as $oldPath) {
                    FileHelper::deleteFile($oldPath);
                }
            }

            // 3. Reset array path
            $this->supporting_documents_paths = [];

            // 4. Proses penyimpanan file baru
            foreach ($this->supporting_documents as $file) {
                // FileHelper::compressAndStore akan menyimpan file asli jika bukan gambar
                $this->supporting_documents_paths[] = FileHelper::compressAndStore(
                    $file,
                    'incident/supporting_documents/documentation'
                );
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 5. AUTO-CLEAR: Jika ada file yang tidak sesuai format (misal user upload .exe atau .zip)
            // Kita reset agar state di UI kembali bersih
            $this->supporting_documents = [];

            throw $e;
        }
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
        if (!in_array($this->consequence_id, [3, 4, 5])) {
            $this->penerimaan_komentar_ktt_id = null;
            $this->penerimaan_komentar_ktt = '';

            // Pastikan array searchName dibersihkan agar UI Select2 sinkron
            if (isset($this->searchNamePenerimaan['ktt'])) {
                $this->searchNamePenerimaan['ktt'] = '';
            }

            // Beritahu JS untuk menghancurkan instance editor (opsional tapi bagus untuk memori)
            $this->dispatch('reset-all-editors');
        } else {
            // Jika berubah ke 3, 4, atau 5, beri sinyal kecil untuk re-init jika diperlukan
            // Livewire v4 biasanya menangani ini lewat x-data init, tapi dispatch membantu jika ada delay render
            $this->dispatch('refresh-ktt-editor');
        }
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

    // Involved Personnel

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



    // Fungsi Pencarian (Dipicu oleh modelsearch di component)
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

    // Data opsi sesuai gambar
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
    public function addCorrectiveRow()
    {
        // 1. Definisikan struktur array dengan lengkap
        $this->corrective_actions[] = [
            'action_description' => '',
            'control_hierarchy' => '', // Tambahkan ini agar select tidak error
            'name' => '',
            'due_date' => null,
            'actual_completion_date' => null,
            'inspector_id' => null,
            'id_number' => '', // Tambahkan jika digunakan di selectActPelapor
            'dept_con' => '',  // Tambahkan jika digunakan di selectActPelapor
        ];

        $index = count($this->corrective_actions) - 1;

        // 2. Inisialisasi pendukung UI
        $this->searchPetugas[$index] = '';
        $this->showDropdownPetugas[$index] = false;

        // JANGAN tambahkan $this->corrective_actions[$index] = []; di sini
    }

    /**
     * Menghapus baris tertentu
     */
    public function removeCorrectiveRow($index)
    {
        unset($this->corrective_actions[$index]);
        unset($this->searchPetugas[$index]);
        unset($this->showDropdownPetugas[$index]);

        // Reset array keys agar urutan index tetap konsisten (0, 1, 2...)
        $this->corrective_actions = array_values($this->corrective_actions);
        $this->searchPetugas = array_values($this->searchPetugas);
        $this->showDropdownPetugas = array_values($this->showDropdownPetugas);
    }

    /**
     * Lifecycle Hook: Berjalan otomatis saat $searchPetugas diupdate
     * Format: updatedFieldNameIndex
     */
    public function updatedSearchPetugas($value, $key)
    {
        // Livewire v4 mengirim key berupa index (misal: "0")
        // Jika format modelsearch adalah searchPetugas.{{ $index }}
        $index = explode('.', $key)[0];

        if (strlen($value) > 1) {
            $this->pelaporsAct = User::where('name', 'like', '%' . $value . '%')
                ->orderBy('name')
                ->limit(20)
                ->get();

            // Pastikan hanya baris ini yang dropdown-nya terbuka
            $this->showDropdownPetugas[$index] = true;
        } else {
            $this->showDropdownPetugas[$index] = false;
        }
    }

    public function selectActPelapor($id, $name)
    {
        // Cari index mana yang dropdown-nya sedang terbuka (true)
        $index = collect($this->showDropdownPetugas)->search(true);

        if ($index !== false) {
            $inspector = User::find($id);

            if ($inspector) {
                // CARA TERBAIK: Update key spesifik tanpa menghapus data lama (action_description, dll)
                $this->corrective_actions[$index]['name'] = $inspector->name;
                $this->corrective_actions[$index]['id_number'] = $inspector->employee_id;
                $this->corrective_actions[$index]['dept_con'] = $inspector->department_name;
                $this->corrective_actions[$index]['inspector_id'] = $inspector->id;

                // Atau jika ingin menggunakan array_merge:
                // $this->corrective_actions[$index] = array_merge($this->corrective_actions[$index], [
                //     'name' => $inspector->name,
                //     'inspector_id' => $inspector->id,
                //     'id_number' => $inspector->employee_id,
                //     'dept_con' => $inspector->department_name,
                // ]);
            }

            // Update search input agar input field menampilkan nama pilihan
            $this->searchPetugas[$index] = $name;

            // Tutup dropdown
            $this->showDropdownPetugas[$index] = false;
        }
    }

    public function getPelaporsPenerimaanProperty()
    {
        // Mendeteksi field mana yang sedang diketik berdasarkan activeType
        $searchTerm = '';
        if ($this->activeTypePenerimaan == 'penerimaan_komentar_contractor') $searchTerm = $this->searchNamePenerimaan['kontraktor'];
        if ($this->activeTypePenerimaan == 'penerimaan_komentar_internal') $searchTerm = $this->searchNamePenerimaan['internal'];
        if ($this->activeTypePenerimaan == 'penerimaan_komentar_ohs') $searchTerm = $this->searchNamePenerimaan['ohs'];
        if ($this->activeTypePenerimaan == 'penerimaan_komentar_ktt') $searchTerm = $this->searchNamePenerimaan['ktt'];

        if (strlen($searchTerm) < 2) {
            return [];
        }

        return User::where('name', 'like', '%' . $searchTerm . '%')
            ->limit(80)
            ->get();
    }

    /**
     * Helper Function untuk Reset Dropdown
     */
    private function resetDropdowns()
    {
        $this->showPenerimaanKomentarContractorDropdown = false;
        $this->showPenerimaanKomentarInternalDropdown = false;
        $this->showPenerimaanKomentarOhsDropdown = false;
        $this->showPenerimaanKomentarKttDropdown = false;
    }

    /**
     * Action: Pilih Pelapor Contractor
     */
    public function selectPenerimaanKomentarContractor($id, $name)
    {
        $this->penerimaan_komentar_contractor_id = $id;
        $this->searchNamePenerimaan['kontraktor'] = $name;
        $this->resetDropdowns();
    }

    /**
     * Action: Pilih Pelapor Internal
     */
    public function selectPenerimaanKomentarInternal($id, $name)
    {
        $this->penerimaan_komentar_internal_id = $id;
        $this->searchNamePenerimaan['internal'] = $name;
        $this->resetDropdowns();
    }

    /**
     * Action: Pilih Pelapor OHS
     */
    public function selectPenerimaanKomentarOhs($id, $name)
    {
        $this->penerimaan_komentar_ohs_id = $id;
        $this->searchNamePenerimaan['ohs'] = $name;
        $this->resetDropdowns();
    }

    /**
     * Action: Pilih Pelapor KTT
     */
    public function selectPenerimaanKomentarKtt($id, $name)
    {
        $this->penerimaan_komentar_ktt_id = $id;
        $this->searchNamePenerimaan['ktt'] = $name;
        $this->resetDropdowns();
    }

    /**
     * Lifecycle: Monitor perubahan search input untuk memunculkan dropdown
     */
    public function updatedSearchNamePenerimaan($value, $key)
    {
        $this->resetDropdowns();

        if ($key === 'kontraktor') $this->showPenerimaanKomentarContractorDropdown = true;
        if ($key === 'internal') $this->showPenerimaanKomentarInternalDropdown = true;
        if ($key === 'ohs') $this->showPenerimaanKomentarOhsDropdown = true;
        if ($key === 'ktt') $this->showPenerimaanKomentarKttDropdown = true;
    }



    /**
     * Mendefinisikan pesan error kustom
     */

    /**
     * Helper untuk menembakkan event validasi ke frontend
     */
    protected function dispatchValidationEvents($errors)
    {
        $komentarFields = [
            'penerimaan_komentar_contractor',
            'penerimaan_komentar_internal',
            'penerimaan_komentar_ohs',
            'penerimaan_komentar_ktt'
        ];

        foreach ($komentarFields as $field) {
            if ($errors->has($field)) {
                $this->dispatch('validate-' . $field);
            }
        }
    }
    public function save()
    {
        // 1. Jalankan Validasi Global
        $this->validate();
        $lastReport = IncidentReport::latest()->first();
        $nextId = $lastReport ? $lastReport->id + 1 : 1;
        $reportNumber = 'INC-' . now()->format('Ymd') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        try {
            $result = DB::transaction(function () {
                $data = $this->prepareArrayData();

                // A. Simpan Header Utama (Incident Report)
                $report = IncidentReport::create($data['header']);
                $report->risk()->create($data['risk_assessment']);

                // B. Simpan Detail Dampak (Injury vs Damage) - model IncidentImpact
                $report->impact()->create([
                    'is_injury'    => $data['impact_details']['is_injury'],
                    'body_part_id' => $data['impact_details']['is_injury'] ? $data['impact_details']['injury_data']['part_id'] : null,
                    'damage_detail' => !$data['impact_details']['is_injury'] ? $data['impact_details']['damage_data']['detail'] : null,
                ]);

                // C. Simpan Personel Terlibat (Part 2) - model InvolvedPerson
                $report->involvedPersons()->createMany($data['pihak_terlibat']);

                // D. Simpan Tim Investigasi (Part 3) - model InvestigationTeam
                $report->investigationTeams()->createMany($data['tim_investigasi']);

                // E. Simpan Analisis PEEPO (Part 4) - model PeepoAnalysis
                $report->peepoAnalyses()->createMany($data['analisis_peepo']);

                // F. Simpan Timeline & 5-Whys (Part 5) - model TimelineAnalysis
                $report->timelines()->create($this->why_analysis);

                // G. Simpan Tindakan Perbaikan (Part 7) - model CorrectiveAction
                $report->correctiveActions()->createMany($data['tindakan_perbaikan']);

                // H. Proses Upload Dokumentasi (Part 7) - model IncidentAttachment
                // Dokumentasi Visual (Foto/Gambar)
                if ($this->visual_evidence) {
                    foreach ($this->visual_evidence as $file) {
                        $path = $file->store('incident/visuals', 'public');
                        $report->attachments()->create([
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => 'visual'
                        ]);
                    }
                }

                // Dokumen Pendukung (PDF/Doc)
                if ($this->supporting_documents) {
                    foreach ($this->supporting_documents as $doc) {
                        $path = $doc->store('incident/documents', 'public');
                        $report->attachments()->create([
                            'file_path' => $path,
                            'file_name' => $doc->getClientOriginalName(),
                            'file_type' => 'document'
                        ]);
                    }
                }

                return $report;
            });

            // === HAPUS SESSION DI SINI ===
            // Karena data sudah masuk DB, kita tidak butuh draft lagi
            session()->forget('incident_data');

            // 3. Feedback Berhasil
            $this->dispatch('alert', [
                'text' => "Laporan " . $result->report_number . " berhasil dikirim!",
                'duration' => 5000,
                'destination' => '/incident/show/' . $result->id, // Arahkan ke detail laporan
                'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatchValidationEvents($e->validator->errors());
            $firstErrorField = collect($e->validator->errors()->keys())->first();
            $this->goToStepByField($firstErrorField);
            throw $e;
        } catch (\Exception $e) {
            // Log error untuk mempermudah debugging sistem SENTRY
            Log::error('Gagal menyimpan Incident Report: ' . $e->getMessage());
            $errorMessage = "Gagal menyimpan laporan: " . $e->getMessage();

            $this->dispatch('alert', [
                'text' => $errorMessage,
                'duration' => 7000,
                'backgroundColor' => "background: #f44336;",
            ]);
        }
    }


    protected function prepareArrayData()
    {
        // Logika Generate Report Number
        $lastReport = IncidentReport::latest()->first();
        $nextId = $lastReport ? $lastReport->id + 1 : 1;
        $reportNumber = 'INC-' . now()->format('Ymd') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        return [
            // KELOMPOK 1: INFORMASI DASAR (Header)
            'header' => [
                'report_number'     => $reportNumber, // <--- TAMBAHKAN INI
                'event_type_id'     => $this->event_type_id,
                'event_sub_type_id' => $this->event_sub_type_id,
                'date_time'         => $this->date_time,
                'location_id'       => $this->location_id,
                'location_specific' => $this->location_specific,
                'department_id'     => $this->department_id,
                'contractor_id'     => $this->contractor_id,
                'penanggung_jawab'  => $this->penanggungJawab,
                'pelapor_id'        => $this->pelapor_id,
                'manual_pelapor'    => $this->manualPelaporName,
                'description'       => $this->description, // 5W+1H
                'emergency_action'  => $this->emergency_action,
                // DATA DARI PART 8 (Key Learning)
                'key_learning'  => $this->key_learning,
                // PART 9: KOMENTAR & APPROVAL
                'pm_contractor_comment' => $this->penerimaan_komentar_contractor,
                'pm_contractor_id'      => $this->penerimaan_komentar_contractor_id,

                'pm_internal_comment'   => $this->penerimaan_komentar_internal,
                'pm_internal_id'        => $this->penerimaan_komentar_internal_id,

                'ohs_head_comment'      => $this->penerimaan_komentar_ohs,
                'ohs_head_id'           => $this->penerimaan_komentar_ohs_id,

                // Logika kondisional KTT (Hanya Level 3, 4, 5)
                'ktt_comment'           => in_array((int)$this->consequence_id, [3, 4, 5])
                    ? $this->penerimaan_komentar_ktt : null,
                'ktt_id'                => in_array((int)$this->consequence_id, [3, 4, 5])
                    ? $this->penerimaan_komentar_ktt_id : null,
            ],

            // KELOMPOK 2: RISK MATRIX INTEGRATION
            'risk_assessment' => [
                'likelihood_id'  => $this->likelihood_id,
                'consequence_id' => $this->consequence_id,
                // Data tambahan dari model RiskAssessment yang tampil di UI
                'rating_name'    => $this->RiskAssessment?->name,
                'deadline'       => $this->RiskAssessment?->notes,
            ],

            // KELOMPOK 3: KATEGORI BAHAYA & DAMPAK (Injury vs Damage)
            'impact_details' => [
                // Logika isInjury
                'is_injury'     => $this->isInjury,
                'injury_data'   => $this->isInjury ? [
                    'category' => $this->selectedBodyPartCategory,
                    'part_id'  => $this->selectedBodyPart,
                ] : null,

                'damage_data'   => !$this->isInjury ? [
                    'detail'   => $this->damage_detail,
                ] : null,
            ],

            // KELOMPOK PART 2: PERSONEL TERLIBAT LANGSUNG
            'pihak_terlibat' => collect($this->directly_involved)->map(function ($person, $index) {
                return [
                    'employee_id'      => $person['employee_id'] ?? null, // ID dari DB jika ada
                    'employee_name'    => $person['employee_name'],
                    'employee_nik'     => $person['employee_nik'],
                    'dept_cont'        => $person['dept_cont'],
                    'jabatan'          => $person['jabatan'],
                    'roster'           => $person['roster'],
                    'shift'            => $person['sift'], // Sesuaikan typo 'sift' dari model Anda
                    'keterlibatan'     => $person['keterlibatan'],
                    'pengalaman_kerja' => $person['pengalaman_kerja'],
                ];
            })->toArray(),

            // KELOMPOK PART 3: TIM INVESTIGASI
            'tim_investigasi' => collect()
                ->concat(collect($this->pemimpin)->map(fn($item) => array_merge($item, ['role' => 'Pemimpin'])))
                ->concat(collect($this->facilitator)->map(fn($item) => array_merge($item, ['role' => 'Facilitator'])))
                ->concat(collect($this->anggota)->map(fn($item) => array_merge($item, ['role' => 'Anggota'])))
                ->map(function ($member) {
                    return [
                        'user_id' => $member['user_id'],
                        'dept'    => $member['dept'],
                        'jabatan' => $member['jabatan'],
                        'role'    => $member['role'],
                    ];
                })->toArray(),

            // KELOMPOK PART 4: ANALISIS PEEPO
            'analisis_peepo' => collect($this->peepoFactors)->map(function ($label, $key) {
                return [
                    'factor_name' => $label, // e.g., People, Equipment
                    'factor_key'  => $key,   // e.g., P, E, E, P, O
                    'temuan'      => $this->peepo[$key]['temuan'] ?? null,
                    'deskripsi'   => $this->peepo[$key]['deskripsi'] ?? null,
                ];
            })->values()->toArray(),


            // KELOMPOK PART 5: 5-WHYS ANALYSIS (Single Row)
            'analysis_timeline' => [
                'original_description' => $this->description,
                'why_count_used'       => $this->whyCount,
                // Menggabungkan semua why1, why2... ke dalam satu array/JSON
                'analysis_steps'       => $this->why_analysis,
            ],

            // KELOMPOK PART 6: ANALISIS PENYEBAB (SCAT)
            'penyebab_insiden' => [
                // 1. PENYEBAB LANGSUNG
                'langsung' => [
                    'kondisi_tidak_aman' => $this->unsafe_conditions,
                    'perilaku_tidak_aman' => $this->unsafe_acts,
                ],

                // 2. PENYEBAB DASAR
                'dasar' => [
                    'faktor_pribadi'   => $this->personal_factors,
                    'faktor_pekerjaan' => $this->job_factors,
                    'sistem_kontrol'   => $this->control_system_factors,
                ],
            ],

            // KELOMPOK PART 7: DOKUMENTASI & TINDAKAN PERBAIKAN
            'dokumentasi' => [
                'visual_evidence'      => $this->visual_evidence, // Pastikan diproses dengan store() nanti
                'supporting_documents' => $this->supporting_documents,
            ],

            'tindakan_perbaikan' => collect($this->corrective_actions)->map(function ($action) {
                return [
                    'action_description'     => $action['action_description'],
                    'hierarchy'       => $action['control_hierarchy'],
                    'pic_user_id'     => $action['inspector_id'], // ID dari searchable select
                    'due_date'        => $action['due_date'],
                    'completion_date' => $action['actual_completion_date'] ?? null,
                    'status'          => !empty($action['actual_completion_date']) ? 'Closed' : 'Open',
                ];
            })->toArray(),


        ];
    }
    private function goToStepByField($field)
    {
        // Mapping field ke Part/Step yang sesuai
        // Sesuaikan dengan name field yang ada di Part 1 - 9 Anda
        if (in_array($field, [
            // 1. Tipe & Jenis
            'event_type_id',
            'event_sub_type_id',

            // 2. Waktu & Lokasi
            'date_time',
            'location_id',
            'location_specific',

            // 3. Organisasi & PIC
            'department_id',
            'contractor_id',
            'penanggungJawab',
            'pelapor_id',
            'manualPelaporName',

            // 4. Integrasi Risk Matrix
            'consequence_id',
            'likelihood_id',

            // 5. Narasi & Tindakan Segera
            'description',
            'emergency_action',

            // 6. Dampak (Injury vs Damage)
            'selectedBodyPartCategory',
            'selectedBodyPart',
            'damage_detail'
        ])) {
            $this->currentStep = 1;
        } elseif (str_starts_with($field, 'directly_involved')) {
            $this->currentStep = 2;
        } elseif (collect(['pemimpin', 'facilitator', 'anggota'])->some(fn($p) => str_starts_with($field, $p))) {
            $this->currentStep = 3;
        } elseif (str_starts_with($field, 'peepo')) {
            $this->currentStep = 4;
        } elseif (str_starts_with($field, 'timelines')) {
            $this->currentStep = 5;
        } elseif (collect([
            'unsafe',
            'personal_factors',
            'job_factors',
            'control_system_factors'
        ])->some(fn($p) => str_starts_with($field, $p))) {
            $this->currentStep = 6;
        } elseif (collect(['visual_evidence', 'supporting_documents', 'corrective_actions'])->some(fn($p) => str_starts_with($field, $p))) {
            $this->currentStep = 7;
        } elseif ($field === 'key_learning') {
            $this->currentStep = 8;
        } elseif (str_starts_with($field, 'penerimaan_komentar')) {
            $this->currentStep = 9;
        }

        // Scroll ke atas agar user sadar ada yang error
        $this->dispatch('scroll-to-top');
    }
    /**
     * Memeriksa apakah suatu field error berada di step tertentu.
     * Digunakan untuk indikator error di UI (Tab/Collapse).
     */
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

                case 7:
                    if (collect(['visual_evidence', 'supporting_documents', 'corrective_actions'])->some(fn($p) => str_starts_with($field, $p))) return true;
                    break;

                case 8:
                    if ($field === 'key_learning') return true;
                    break;

                case 9:
                    if (str_starts_with($field, 'penerimaan_komentar')) return true;
                    break;
            }
        }

        return false;
    }
}

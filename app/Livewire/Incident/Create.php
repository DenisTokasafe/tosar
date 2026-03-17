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
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use \App\Traits\WithDeptContSelection;
use App\Traits\WithSearchLocation;
use App\Traits\WithSearchPelapor;

class Create extends Component
{
    use WithFileUploads, WithPagination, WithDeptContSelection, WithSearchLocation, WithSearchPelapor;

    public $event_type_id, $event_sub_type_id, $description, $location_id, $location_specific;
    public $date_time, $pelapor_id, $manualPelaporName;
    public $kondisi_tidak_aman, $tindakan_tidak_aman;
    public $department_id, $contractor_id, $penanggungJawab;
    public $deptCont = 'dept';
    public $keyWord = 'kta';
    public $likelihood_id, $consequence_id, $emergency_action;
    public $damage_detail, $selectedBodyPartCategory, $selectedBodyPart;

    public $likelihoods = [], $consequences = [],
        $location_spesific,
        $documentation,
        $visual_evidence, $visual_evidence_path,
        $supporting_documents, $supporting_documents_path;
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


    // Pastikan Anda menginisialisasi array penampung data di mount
    public $peepo = [];
    public $timelines = [];
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
    public $pelaporsAct = [];           // Hasil query pencarian (biasanya global atau di-filter)
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

    public $searchNamePenerimaan = [
        'kontraktor' => '',
        'internal' => '',
        'ohs' => '',
        'ktt' => '',
    ];

    protected function rules()
    {
        return [
            'event_type_id' => 'required|exists:event_types,id',
            'event_sub_type_id' => 'required|exists:event_sub_types,id',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'location_specific' => 'required_with:location_id|string',
            'date_time' => 'required|date',
            'pelapor_id' => 'required_without:manualPelaporName',

            // Mutual Exclusion KTA/TTA
            'kondisi_tidak_aman' => 'nullable|required_without:tindakan_tidak_aman',
            'tindakan_tidak_aman' => 'nullable|required_without:kondisi_tidak_aman',

            // Mutual Exclusion Dept/Contractor
            'department_id' => 'nullable|required_without:contractor_id|exists:departments,id',
            'contractor_id' => 'nullable|required_without:department_id|exists:contractors,id',

            'deptCont' => 'required',
            'keyWord' => 'required',
            'likelihood_id' => 'required',
            'consequence_id' => 'required',
            'emergency_action' => 'required',
            'penanggungJawab' => 'required',
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
    }
    /**
     * Mengembalikan atribut validasi yang sudah diterjemahkan.
     */
    protected function validationAttributes()
    {
        return [
            // Pelapor & Lokasi
            'pelapor_id'        => __('Nama Pelapor'),
            'manualPelaporName' => __('Nama Pelapor Manual'),
            'event_type_id'     => __('Tipe Kejadian'),
            'event_sub_type_id' => __('Sub Tipe Kejadian'),
            'description'       => __('Deskripsi Kejadian'),
            'location_id'       => __('Lokasi Utama'),
            'location_specific' => __('Detail Lokasi Spesifik'),
            'date_time'         => __('Tanggal dan Waktu'),

            // KTA & TTA
            'kondisi_tidak_aman'  => __('Kategori Kondisi Tidak Aman'),
            'tindakan_tidak_aman' => __('Kategori Tindakan Tidak Aman'),
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

            // Komentar Penerimaan (Komentar yang tadi Anda tambahkan)
            'penerimaan_komentar_contractor_id' => __('Penanggung Jawab Kontraktor'),
            'penerimaan_komentar_internal_id'   => __('Penanggung Jawab Internal'),
            'penerimaan_komentar_ohs_id'        => __('Penanggung Jawab OHS'),
            'penerimaan_komentar_contractor'    => __('Komentar Kontraktor'),
            'penerimaan_komentar_internal'      => __('Komentar Internal'),
            'penerimaan_komentar_ohs'           => __('Komentar OHS'),
            'penerimaan_komentar_ktt'           => __('Komentar KTT'),
        ];
    }

    public function updated($propertyName)
    {
        // Setiap kali ada perubahan, validasi field tersebut
        $this->validateOnly($propertyName);

        // Jika tipe event berubah, validasi ulang field kondisional
        if ($propertyName === 'event_type_id') {
            $this->validateOnly('selectedBodyPartCategory');
            $this->validateOnly('selectedBodyPart');
            $this->validateOnly('damage_detail');
        }
        // 3. Logika Dispatch untuk Komentar Penerimaan

    }

    // Komentar Standard
    protected function messages()
    {
        return [
            'required' => __(':attribute wajib diisi.'),
            'exists'   => __('Pilihan :attribute tidak valid.'),
            'min'      => __(':attribute minimal harus :min karakter.'),
            'date'     => __('Format tanggal :attribute tidak sesuai.'),

            // Pesan Custom untuk SENTRY
            'kondisi_tidak_aman.required_without'   => __('Mohon isi Kondisi atau Tindakan Tidak Aman.'),
            'department_id.required_without'        => __('Silakan pilih Departemen atau Kontraktor.'),
            'tindakan_tidak_aman.required_without'  => __('Mohon isi Tindakan Tidak Aman atau Kondisi Tidak Aman (salah satu wajib).'),
            'contractor_id.required_without'        => __('Pilih Kontraktor atau Department terkait.'),
        ];
    }

    public $currentStep = 1;
    public $totalSteps = 9;

    // Fungsi untuk pindah step
    public function setStep($step)
    {
        // Sebelum pindah ke step berikutnya, validasi data step saat ini
        if ($step > $this->currentStep) {
            $this->validateCurrentStep();
        }

        $this->currentStep = $step;
    }



    // State untuk menampilkan dropdown
    public $showPenerimaanKomentarContractorDropdown = false;
    public $showPenerimaanKomentarInternalDropdown = false;
    public $showPenerimaanKomentarOhsDropdown = false;
    public $showPenerimaanKomentarKttDropdown = false;

    // State tambahan untuk tracking fokus (opsional, sesuai Blade kamu)
    public $activeTypePenerimaan = '';
    public $activeIndexPenerimaan = null;
    public function mount()
    {

        if (session()->has('incident_data')) {
            $this->fill(session('incident_data'));
        }
        $this->likelihoods = Likelihood::orderByDesc('level')->get();
        $this->consequences = RiskConsequence::orderBy('level')->get();

        if (empty($this->directly_involved)) {
            $this->addDirectlyInvolvedRow();
        }
        $this->addRow('pemimpin');
        $this->addRow('facilitator');
        $this->addRow('anggota');
        $this->addRow('timelines');
        $this->addRow('unsafe_conditions');
        $this->addRow('unsafe_acts');
        $this->addRow('personal_factors');
        $this->addRow('job_factors');
        $this->addRow('control_system_factors');
        $this->addCorrectiveRow();

        foreach ($this->peepoFactors as $key => $label) {
            $this->peepo[$key] = [
                'temuan' => '',
                'deskripsi' => ''
            ];
        }
    }

    public function updatedKeyWord($value)
    {
        // Bersihkan nilai yang tidak terpilih agar validasi required_without atau required_if tidak bentrok
        if ($value === 'kta') {
            $this->tindakan_tidak_aman = null;
        } elseif ($value === 'tta') {
            $this->kondisi_tidak_aman = null;
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
        // Cari data lengkap employee
        $employee = User::find($id);

        if ($employee) {
            $this->directly_involved[$index]['employee_id'] = $employee->id;
            $this->directly_involved[$index]['employee_name'] = $employee->name;
            $this->directly_involved[$index]['employee_nik'] = $employee->employee_id;
            $this->directly_involved[$index]['dept_cont'] = $employee->department_name;

            // Isi search input dengan nama yang dipilih agar dropdown tertutup/sinkron
            $this->searchKorban[$index] = $name;
            $this->show_employee_dropdown[$index] = false;
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
    }
    #[Computed]
    public function gridClass()
    {
        if ($this->whyCount == 2) {
            return 'grid-cols-2';
        }

        if ($this->whyCount >= 3) {
            return 'grid-cols-3';
        }

        return 'grid-cols-1';
    }
    public function addWhyColumn()
    {
        $this->whyCount++;

        // Inisialisasi key baru di setiap baris timeline agar tidak error
        foreach ($this->timelines as $index => $line) {
            $this->timelines[$index]["why{$this->whyCount}"] = '';
        }
    }
    public function removeWhyColumn()
    {
        if ($this->whyCount > 1) {
            // Hapus key "why" terakhir dari setiap baris timeline
            foreach ($this->timelines as $index => $line) {
                unset($this->timelines[$index]["why{$this->whyCount}"]);
            }
            $this->whyCount--;
        }
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
            // Ambil data lama agar tidak hilang jika tidak ingin dioverwrite total
            // Atau langsung set seperti di bawah ini:
            $this->{$type}[$index]['user_id'] = $user->id;
            $this->{$type}[$index]['nama']    = $user->name;

            // Hanya isi jabatan/dept otomatis jika Anda ingin (sebagai default),
            // tapi karena user akan isi manual, kita berikan nilai dari DB sebagai saran awal saja.
            $this->{$type}[$index]['jabatan'] = $user->position ?? '';
            $this->{$type}[$index]['dept']    = $user->department_name ?? '';

            // Update teks di input pencarian
            $this->searchQuery[$index][$type] = $user->name;


            // Reset dropdown
            $this->showDropdownPartisipan[$index] = false;
            $this->options = [];
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
        $this->corrective_actions[] = [
            'action_description' => '',
            'name' => '', // Sesuai modelid
            'due_date' => null,
            'actual_completion_date' => null,
            'inspector_id' => null,
        ];

        $index = count($this->corrective_actions) - 1;

        // Inisialisasi pendukung UI
        $this->searchPetugas[$index] = '';
        $this->showDropdownPetugas[$index] = false;
        $this->corrective_actions[$index] = [];
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
            // 1. Simpan data ke array corrective_actions sesuai modelid di Blade
            $this->corrective_actions[$index]['name'] = $name;

            // Ambil detail tambahan dari database
            $inspector = User::find($id);
            if ($inspector) {
                // Jika Anda punya array khusus inspectors untuk detail tambahan
                $this->corrective_actions[$index] = [
                    'name' => $inspector->name,
                    'id_number' => $inspector->employee_id,
                    'dept_con' => $inspector->department_name,
                ];

                // Simpan ID ke corrective_actions untuk foreign key database
                $this->corrective_actions[$index]['inspector_id'] = $inspector->id;
            }

            // 2. Update search input (modelsearch) agar input field menampilkan nama pilihan
            $this->searchPetugas[$index] = $name;

            // 3. Tutup dropdown untuk baris tersebut
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

    public function save()
    {
        $komentarFields = [
            'penerimaan_komentar_contractor',
            'penerimaan_komentar_internal',
            'penerimaan_komentar_ohs',
            'penerimaan_komentar_ktt'
        ];

        foreach ($komentarFields as $field) {
            // Kita gunakan loop karena di fungsi save kita ingin
            // menembakkan event untuk SEMUA field komentar sekaligus
            $this->dispatch('validate-' . $field);
        }
        $this->validate();

        // 2. Validasi tambahan secara dinamis
        $dynamicRules = [];

        if ($this->isInjury()) {
            $dynamicRules['selectedBodyPartCategory'] = 'required';
            $dynamicRules['selectedBodyPart'] = 'required_with:selectedBodyPartCategory|exists:body_parts,id';
        } else {
            $dynamicRules['damage_detail'] = 'required|string|min:5';
        }

        if (in_array((int)$this->consequence_id, [3, 4, 5])) {
            $dynamicRules['penerimaan_komentar_ktt_id'] = 'required|exists:users,id';
            $dynamicRules['penerimaan_komentar_ktt']    = 'required|min:11';
        }

        try {
            // Jalankan validasi dinamis dengan pesan kustom
            $this->validate($dynamicRules, $this->getValidationMessages());
        } catch (ValidationException $e) {
            // 2. Jika kamu ingin lebih spesifik untuk KTT saja (opsional)
            if ($e->validator->errors()->has('penerimaan_komentar_ktt')) {
                $this->dispatch('validate-penerimaan_komentar_ktt');
            }
            if ($e->validator->errors()->has('penerimaan_komentar_kontraktor')) {
                $this->dispatch('validate-penerimaan_komentar_kontraktor');
            }
            if ($e->validator->errors()->has('penerimaan_komentar_internal')) {
                $this->dispatch('validate-penerimaan_komentar_internal');
            }
            if ($e->validator->errors()->has('penerimaan_komentar_ohs')) {
                $this->dispatch('validate-penerimaan_komentar_ohs');
            }

            throw $e;
        }
    }
}

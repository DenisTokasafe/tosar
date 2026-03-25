<section class="w-full">
    <x-toast />

    {{-- Breadcrumb khusus Edit --}}
    <div class="flex justify-start mb-2" wire:ignore>
        {{ Breadcrumbs::render('incident-detail', $incidentId) }}
    </div>

    {{-- Header dengan Nomor Laporan --}}
    <div class="flex flex-col justify-between gap-2 md:flex-row md:items-end">
        <div>
            <flux:heading level="1" class="mb-1 capitalize">
                {{ __('Update Laporan Insiden') }}
            </flux:heading>
            <flux:subheading size="sm" class="text-accent">
                {{ __('Nomor Laporan:') }} <span class="font-bold text-primary">{{ $report_number }}</span>
            </flux:subheading>
        </div>
        <div class="flex gap-2">
            <span class="badge badge-warning font-bold p-4 shadow-sm italic uppercase tracking-widest text-[10px]">Mode Edit SENTRY</span>
        </div>
    </div>

    {{-- SUMMARY WIDGET SENTRY --}}
    <div class="grid grid-cols-1 gap-4 mt-6 mb-2 md:grid-cols-3">
        <div class="border shadow-sm stats border-base-300 bg-base-100">
            <div class="p-4 stat">
                <div class="stat-title text-[10px] uppercase font-bold tracking-tighter">Status Laporan</div>
                <div class="flex items-center gap-2 mt-1 text-lg stat-value">
                    @if($status == 'Open')
                    <div class="badge badge-error badge-xs animate-pulse"></div>
                    <span class="text-sm italic font-black uppercase text-error">OPEN</span>
                    @else
                    <div class="badge badge-success badge-xs"></div>
                    <span class="text-sm italic font-black uppercase text-success">CLOSED</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="border shadow-sm stats border-base-300 bg-base-100">
            <div class="p-4 stat">
                <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-primary">Progress Review</div>
                <div class="flex gap-1 mt-2">
                    {{-- Indikator Step Review (Flat Table Logic) --}}
                    <div class="h-1.5 w-full rounded-full {{ $penerimaan_komentar_contractor_id ? 'bg-success' : 'bg-base-300' }}" title="PM Contractor"></div>
                    <div class="h-1.5 w-full rounded-full {{ $penerimaan_komentar_internal_id ? 'bg-success' : 'bg-base-300' }}" title="PM Internal"></div>
                    <div class="h-1.5 w-full rounded-full {{ $penerimaan_komentar_ohs_id ? 'bg-success' : 'bg-base-300' }}" title="OHS Head"></div>

                    {{-- Indikator KTT hanya muncul jika Rating Sedang, Tinggi, atau Ekstrim --}}
                    @if(in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrim']))
                    <div class="h-1.5 w-full rounded-full {{ $penerimaan_komentar_ktt_id ? 'bg-success' : 'bg-base-300' }}" title="KTT"></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="border shadow-sm stats border-base-300 bg-base-100">
            <div class="p-4 stat">
                <div class="stat-title text-[10px] uppercase font-bold tracking-tighter">Otoritas Terakhir</div>
                <div class="mt-2 text-xs font-medium stat-desc text-base-content">
                    <span class="flex items-center gap-1 italic">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $incident->latest_reviewer_name }}
                    </span>
                    {{-- Menampilkan Badge Otoritas --}}
                    @if($incident->latest_reviewer_role !== 'Pending')
                    <div class="mt-1 lowercase badge badge-outline badge-info badge-xs opacity-70">
                        {{ $incident->latest_reviewer_role }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-incident.layout>
        {{-- Iterasi Collapse --}}
        @for ($i = 1; $i <= 9; $i++)
            @php
            $hasErrorInStep=$errors->any() && $this->isFieldInStep($i, $errors->toArray());

            $stepTitles = [
            1 => 'Detil Laporan',
            2 => 'Pihak Terlibat Langsung (Saksi, korban cedera, kontraktor, operator, dll.)',
            3 => 'Partisipan Investigasi',
            4 => 'PEEPO Investigation questions for identification of the incident factors',
            5 => 'Time Line dan Analisis Informasi',
            6 => 'Investigasi Kecelakaan (Daftar Checklist Mengacu pada TT-MGT-LMS-025A)',
            7 => 'TINDAKAN PERBAIKAN',
            8 => 'Apa Kunci Pembelajaran ?',
            9 => 'PENERIMAAN & KOMENTAR PENJINJAU INVESTIGASI',
            ];

            $canEdit = match($i) {
            1, 2 => Gate::allows('updateInitialData', $incident),
            3, 4, 5, 6 => Gate::allows('conductInvestigation', $incident),
            7 => Gate::allows('manageCorrectiveActions', $incident),
            8 => Gate::allows('updateLessonsLearned', $incident),
            9 => Gate::allows('reviewReport', $incident),
            default => false
            };
            @endphp

            <div
                wire:key="step-edit-container-{{ $i }}"
                class="border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl relative z-0 transition-all duration-300 ease-in-out
                hover:-translate-x-1 hover:shadow-xl hover:z-10
                {{ !$canEdit ? 'bg-base-200/50' : '' }}
                {{ $hasErrorInStep ? 'border-error shadow-md' : 'hover:border-info' }}">

                <input type="radio" name="edit-accordion" wire:click="goToStep({{ $i }})" value="{{ $i }}" {{ $currentStep == $i ? 'checked' : '' }} />

                <div class="flex items-center justify-between font-semibold collapse-title transition-colors duration-300
                    {{ $hasErrorInStep
                        ? 'bg-error text-error-content'
                        : ($currentStep == $i ? 'bg-linear-to-r from-blue-600 to-info text-white' : 'bg-base-200 text-base-content')
                    }}">

                    <h3 class="flex items-center gap-2 text-xs font-bold tracking-wide uppercase">
                        <span>BAGIAN {{ $i }}</span>
                        <span class="hidden md:inline">– {{ $stepTitles[$i] }}</span>

                        @if(!$canEdit)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-[9px] lowercase font-normal opacity-70">(view only)</span>
                        @endif

                        @if($hasErrorInStep)
                        <span class="ml-2 text-white border-none badge badge-sm badge-ghost bg-white/20 animate-pulse">⚠️ ERROR</span>
                        @else
                        @if($currentStep > $i)
                        <span class="px-1 ml-2 text-white border-none badge badge-sm badge-success">✓</span>
                        @endif
                        @endif
                    </h3>
                </div>

                <div class="text-xs collapse-content bg-base-100">
                    <div class="pt-4">
                        @if($hasErrorInStep)
                        <div class="p-2 mb-4 text-xs border rounded-lg bg-error/10 text-error border-error/20">
                            <strong>Perhatian:</strong> Beberapa kolom pada Bagian ini masih memerlukan perbaikan.
                        </div>
                        @endif

                        @include('livewire.incident.step_edit.incident-step-' . $i, ['readonly' => !$canEdit])

                        <div class="flex justify-between pt-4 mt-4 border-t border-base-200">
                            <div>
                                @if($i > 1)
                                <button type="button" wire:click="goToStep({{ $i - 1 }})" class="btn btn-ghost btn-xs">
                                    « Kembali
                                </button>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                {{-- Tombol Navigasi Lanjut --}}
                                @if ($i < 9)
                                    <button wire:click="nextStep" class="px-4 text-white shadow-sm btn btn-info btn-xs">
                                    {{ $canEdit ? 'Simpan & Lanjut »' : 'Lihat Selanjutnya »' }}
                                    </button>
                                    @endif

                                    {{-- Logika Tombol Update SENTRY --}}
                                    <div class="flex flex-col items-end">
                                        @if($this->canUpdate)
                                        {{-- Tombol AKTIF: Memenuhi Policy & Validasi KTT (jika di step 9) --}}
                                        <button type="button"
                                            wire:click="update"
                                            wire:loading.attr="disabled"
                                            class="px-4 text-white shadow-md btn btn-xs btn-success">
                                            <span wire:loading.remove wire:target="update">Update Laporan</span>
                                            <span wire:loading.remove.class="hidden" wire:target="update" class="hidden loading loading-spinner loading-xs"></span>
                                        </button>
                                        @else
                                        {{-- Tombol TERKUNCI --}}
                                        <button disabled class="px-4 opacity-50 btn btn-xs btn-disabled bg-base-300">
                                            <div class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                                <span>Update Locked</span>
                                            </div>
                                        </button>

                                        {{-- Pesan Error Spesifik agar User tidak Bingung --}}
                                        @if($i == 9 && in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrim']) && empty($penerimaan_komentar_ktt_id))
                                        <span class="mt-1 text-[9px] text-error italic animate-pulse">
                                            Otoritas KTT wajib untuk rating {{ $rating_name }}
                                        </span>
                                        @elseif(!$canEdit)
                                        <span class="mt-1 text-[9px] text-warning italic">
                                            Akses edit dibatasi (Policy)
                                        </span>
                                        @endif
                                        @endif
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
    </x-incident.layout>

    @push('scripts')
    <script>
        window.addEventListener('scroll-to-top', event => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
    @endpush
</section>
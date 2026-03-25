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

                    {{-- Indikator KTT hanya muncul jika Rating Sedang, Tinggi, atau Ekstrem --}}
                    @if(in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrem']))
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
        {{-- Iterasi Collapse --}}
        @for ($i = 1; $i <= 9; $i++)
            @php
            // ... (Logika $hasErrorInStep dan $stepTitles tetap sama) ...

            $canEdit=match($i) {
            1, 2=> Gate::allows('updateInitialData', $incident),
            3, 4, 5, 6 => Gate::allows('conductInvestigation', $incident),
            7 => Gate::allows('manageCorrectiveActions', $incident),
            8 => Gate::allows('updateLessonsLearned', $incident),
            9 => Gate::allows('reviewReport', $incident),
            default => false
            };
            @endphp

            <div
                wire:key="step-edit-container-{{ $i }}"
                @class([ 'border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl relative z-0 transition-all duration-300 ease-in-out' , 'hover:-translate-x-1 hover:shadow-xl hover:z-10'=> $canEdit,
                'bg-base-200/50 cursor-not-allowed opacity-80' => !$canEdit, {{-- Visual feedback jika terkunci --}}
                'border-error shadow-md' => $hasErrorInStep,
                'hover:border-info' => $canEdit && !$hasErrorInStep
                ])>

                {{-- MODIFIKASI DISINI: Tambahkan 'disabled' jika !$canEdit --}}
                <input
                    type="radio"
                    name="edit-accordion"
                    wire:click="{{ $canEdit ? "goToStep($i)" : "" }}"
                    value="{{ $i }}"
                    {{ $currentStep == $i ? 'checked' : '' }}
                    {{ !$canEdit ? 'disabled' : '' }} {{-- Mencegah collapse terbuka --}} />

                <div @class([ 'flex items-center justify-between font-semibold collapse-title transition-colors duration-300' , 'bg-error text-error-content'=> $hasErrorInStep,
                    'bg-linear-to-r from-blue-600 to-info text-white' => $currentStep == $i,
                    'bg-base-200 text-base-content' => $currentStep != $i && $canEdit,
                    'bg-base-300 text-base-content/50' => !$canEdit {{-- Warna header jika terkunci --}}
                    ])>

                    <h3 class="flex items-center gap-2 text-xs font-bold tracking-wide uppercase">
                        <span>BAGIAN {{ $i }}</span>
                        <span class="hidden md:inline">– {{ $stepTitles[$i] }}</span>

                        @if(!$canEdit)
                        <x-icon name="lock" class="w-3 h-3 opacity-40" />
                        <span class="text-[9px] lowercase font-normal opacity-60">(akses terbatas)</span>
                        @endif

                        {{-- ... (Status Error/Success Icon tetap sama) ... --}}
                    </h3>
                </div>

                <div class="text-xs collapse-content bg-base-100">
                    {{-- Isi konten hanya akan dirender jika input di atas bisa diklik --}}
                    <div class="pt-4">
                        @include('livewire.incident.step_edit.incident-step-' . $i, ['readonly' => !$canEdit])

                        {{-- ... (Footer navigasi tetap sama) ... --}}
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
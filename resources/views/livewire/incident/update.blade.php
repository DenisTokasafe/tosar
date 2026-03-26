<section class="w-full">
    <x-toast />

    {{-- Breadcrumb khusus Edit --}}
    <div class="flex justify-start mb-2" wire:ignore>
        {{ Breadcrumbs::render('incident-detail', $incidentId) }}
    </div>

    <div class="space-y-6">
        {{-- HEADER SECTION --}}
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <flux:heading level="1" class="mb-1 text-xl capitalize md:text-2xl">
                    {{ __('Update Laporan Insiden') }}
                </flux:heading>
                <flux:subheading size="sm" class="flex items-center gap-2 text-accent">
                    {{ __('Nomor Laporan:') }}
                    <span class="font-black tracking-tight text-primary">{{ $report_number }}</span>
                </flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge badge-warning font-bold p-4 shadow-sm italic uppercase tracking-widest text-[10px] w-full md:w-auto justify-center">
                    Mode Edit SENTRY
                </span>
            </div>
        </div>

        {{-- TOTAL PROGRESS BAR (Optional but Recommended) --}}
        <div class="w-full bg-base-200 rounded-full h-1.5 mb-2 overflow-hidden shadow-inner">
            <div class="h-full transition-all duration-700 ease-in-out bg-primary" style="width: {{ $this->getProgressPercentage() }}%"></div>
        </div>

        {{-- SUMMARY WIDGET SENTRY --}}
        {{-- Responsive: 1 kolom di HP, 2 kolom di Tablet, 3 kolom di Desktop --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- STATS: STATUS --}}
            <div class="overflow-visible border shadow-sm stats border-base-300 bg-base-100">
                <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-base-content/60">Update Laporan Insiden</div>
                <flux:subheading size="sm" class="flex items-center gap-2 text-accent">
                    {{ __('Nomor Laporan:') }}
                    <span class="font-black tracking-tight text-primary">{{ $report_number }}</span>
                </flux:subheading>
            </div>

            {{-- STATS: STATUS --}}
            <div class="overflow-visible border shadow-sm stats border-base-300 bg-base-100">
                <div class="p-4 stat">
                    <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-base-content/60">Status Laporan</div>
                    <div class="flex items-center gap-2 mt-1 stat-value">
                        @switch($status)
                        @case('Open')
                        @case('Reported')
                        <div class="badge badge-error badge-xs animate-pulse"></div>
                        <span class="text-sm italic font-black uppercase text-error">OPEN / REPORTED</span>
                        @break
                        @case('In Progress')
                        <div class="badge badge-info badge-xs animate-bounce"></div>
                        <span class="text-sm italic font-black uppercase text-info">IN PROGRESS</span>
                        @break
                        @case('Action Required')
                        <div class="badge badge-warning badge-xs"></div>
                        <span class="text-sm italic font-black uppercase text-warning">ACTION REQUIRED</span>
                        @break
                        @case('Closed')
                        <div class="badge badge-success badge-xs"></div>
                        <span class="text-sm italic font-black uppercase text-success">CLOSED</span>
                        @break
                        @endswitch
                    </div>
                </div>
            </div>

            {{-- STATS: REVIEW PROGRESS --}}
            <div class="border shadow-sm stats border-base-300 bg-base-100">
                <div class="p-4 stat">
                    <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-primary">Progress Review</div>
                    <div class="flex items-end justify-between gap-1 mt-2">
                        <div class="flex-1 space-y-1">
                            <div class="flex gap-1">
                                <div class="h-2 w-full rounded-full {{ $penerimaan_komentar_contractor_id ? 'bg-success' : 'bg-base-300' }}" title="PM Contractor"></div>
                                <div class="h-2 w-full rounded-full {{ $penerimaan_komentar_internal_id ? 'bg-success' : 'bg-base-300' }}" title="PM Internal"></div>
                                <div class="h-2 w-full rounded-full {{ $penerimaan_komentar_ohs_id ? 'bg-success' : 'bg-base-300' }}" title="OHS Head"></div>
                                @if(in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrem']))
                                <div class="h-2 w-full rounded-full {{ $penerimaan_komentar_ktt_id ? 'bg-success' : 'bg-base-300' }}" title="KTT"></div>
                                @endif
                            </div>
                            <div class="text-[9px] text-base-content/50 font-medium uppercase tracking-tighter flex justify-between">
                                <span>Contractor</span>
                                <span>OHS / KTT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS: OTORITAS (Full width di Tablet agar seimbang) --}}
            <div class="border shadow-sm stats border-base-300 bg-base-100 sm:col-span-2 lg:col-span-1">
                <div class="p-4 stat">
                    <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-base-content/60">Otoritas Terakhir</div>
                    <div class="mt-1 text-xs font-medium stat-desc text-base-content">
                        <div class="flex items-center gap-2 truncate">
                            <div class="avatar placeholder">
                                <div class="w-6 rounded-full bg-neutral text-neutral-content">
                                    <span class="text-[10px]">{{ substr($incident->latest_reviewer_name ?? 'N', 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <span class="italic font-bold truncate">{{ $incident->latest_reviewer_name ?? 'N/A' }}</span>
                                @if($incident->latest_reviewer_role && $incident->latest_reviewer_role !== 'Pending')
                                <span class="text-[9px] text-info uppercase font-bold tracking-widest">{{ $incident->latest_reviewer_role }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-incident.layout>
        {{-- Iterasi Collapse SENTRY --}}
        @for ($i = 1; $i <= 9; $i++)
            @php
            // Logic Deteksi Error yang lebih akurat
            $fieldsInStep=$this->getFieldsForStep($i);
            $hasErrorInStep = false;
            foreach($fieldsInStep as $field) {
            if ($errors->has($field) || $errors->has($field . '.*')) {
            $hasErrorInStep = true;
            break;
            }
            }

            $stepTitles = [
            1 => 'Detil Laporan',
            2 => 'Pihak Terlibat Langsung',
            3 => 'Partisipan Investigasi',
            4 => 'PEEPO Investigation Factor',
            5 => 'Time Line & Analisis',
            6 => 'Investigasi Kecelakaan (Checklist)',
            7 => 'Tindakan Perbaikan',
            8 => 'Kunci Pembelajaran',
            9 => 'Penerimaan & Komentar Reviewer',
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
                @class([ 'border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl relative z-0 transition-all duration-300 ease-in-out' , 'hover:-translate-x-1 hover:shadow-xl hover:z-10'=> $canEdit,
                'bg-base-200/50 cursor-not-allowed opacity-80' => !$canEdit,
                'border-error shadow-md' => $hasErrorInStep,
                'hover:border-info' => $canEdit && !$hasErrorInStep
                ])>

                <input
                    type="radio"
                    name="edit-accordion"
                    wire:click="{{ $canEdit ? "goToStep($i)" : "" }}"
                    value="{{ $i }}"
                    {{ $currentStep == $i ? 'checked' : '' }}
                    {{ !$canEdit ? 'disabled' : '' }} />

                <div @class([ 'flex items-center justify-between font-semibold collapse-title transition-colors duration-300' , 'bg-error text-error-content'=> $hasErrorInStep,
                    'bg-linear-to-r from-blue-600 to-info text-white' => $currentStep == $i && !$hasErrorInStep,
                    'bg-base-200 text-base-content' => $currentStep != $i && $canEdit,
                    'bg-base-300 text-base-content/40' => !$canEdit
                    ])>

                    <h3 class="flex items-center gap-2 text-xs font-bold tracking-wide uppercase">
                        <span>BAGIAN {{ $i }}</span>
                        <span class="hidden md:inline">– {{ $stepTitles[$i] }}</span>

                        @if(!$canEdit)
                        <x-icon name="lock-closed" class="w-3 h-3 opacity-60" />
                        <span class="text-[9px] lowercase font-normal opacity-70">(akses terbatas)</span>
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
                            <strong>Perhatian:</strong> Beberapa kolom pada bagian ini memerlukan perbaikan sebelum laporan dapat diperbarui.
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
                                @if ($i < 9)
                                    <button wire:click="nextStep" class="px-4 text-white shadow-sm btn btn-info btn-xs">
                                    {{ $canEdit ? 'Simpan & Lanjut »' : 'Lihat Selanjutnya »' }}
                                    </button>
                                    @endif

                                    <div class="flex flex-col items-end">
                                        @if($this->canUpdate)
                                        <button type="button"
                                            wire:click="update"
                                            wire:loading.attr="disabled"
                                            class="px-4 text-white shadow-md btn btn-xs btn-success">
                                            <span wire:loading.remove wire:target="update">Update Laporan</span>
                                            <span wire:loading wire:target="update" class="loading loading-spinner loading-xs"></span>
                                        </button>
                                        @else
                                        <button disabled class="px-4 opacity-50 btn btn-xs btn-disabled bg-base-300">
                                            <div class="flex items-center gap-1">
                                                <x-icon name="lock-closed" class="w-3 h-3" />
                                                <span>Update Terkunci</span>
                                            </div>
                                        </button>

                                        @if($i == 9 && in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrem']) && empty($penerimaan_komentar_ktt_id))
                                        <span class="mt-1 text-[9px] text-error italic animate-pulse">
                                            Otoritas KTT wajib untuk rating {{ $rating_name }}
                                        </span>
                                        @elseif(!$canEdit)
                                        <span class="mt-1 text-[9px] text-warning italic">
                                            Akses terbatas (HSE Policy)
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
<section class="w-full">
    <x-toast />

    {{-- Breadcrumb khusus Edit --}}
    <div class="flex justify-start mb-2" wire:ignore>
        {{ Breadcrumbs::render('incident-detail', $incidentId) }}
    </div>

    <div class="space-y-6">
        {{-- HEADER SECTION --}}
        <div class="w-full bg-base-200 rounded-full h-1.5 mb-2 overflow-hidden shadow-inner">
            <div class="h-full transition-all duration-700 ease-in-out bg-primary" style="width: {{ $this->getProgressPercentage() }}%"></div>
        </div>

        {{-- SUMMARY WIDGET SENTRY --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- STATS: NOMOR LAPORAN --}}
            <div class="overflow-visible border shadow-sm stats border-base-300 bg-base-100">
                <div class="p-2 stat">
                    <flux:button size="xs" variant="accent" icon='clock' onclick="my_modal_2.showModal()"></flux:button>
                    <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-base-content/60">Update Laporan Insiden</div>
                    <flux:subheading size="sm" class="flex items-center gap-2 text-accent">
                        {{ __('Nomor Laporan:') }}
                        <span class="font-black tracking-tight text-primary">{{ $report_number }}</span>
                    </flux:subheading>
                </div>
            </div>

            {{-- STATS: STATUS --}}
            <div class="overflow-visible border shadow-sm stats border-base-300 bg-base-100">
                <div class="p-2 stat">
                    <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-base-content/60">Status Laporan</div>
                    <div class="flex items-center gap-2 mt-1 stat-value">
                        @switch($status)
                        @case('Open') @case('Reported')
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
                <div class="p-2 stat">
                    <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-primary">Progress Review</div>
                    <div class="flex items-end justify-between gap-1 mt-2">
                        <div class="flex-1 space-y-1">
                            <div class="flex gap-1">
                                @if($contractor_id)
                                <div class="h-2 w-full rounded-full {{ $penerimaan_komentar_contractor_id ? 'bg-success' : 'bg-base-300' }}" title="PM Contractor"></div>
                                @endif
                                <div class="h-2 w-full rounded-full {{ $penerimaan_komentar_internal_id ? 'bg-success' : 'bg-base-300' }}" title="PM Internal"></div>
                                <div class="h-2 w-full rounded-full {{ $penerimaan_komentar_ohs_id ? 'bg-success' : 'bg-base-300' }}" title="OHS Head"></div>
                                @if(in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrem']))
                                <div class="h-2 w-full rounded-full {{ $penerimaan_komentar_ktt_id ? 'bg-success' : 'bg-base-300' }}" title="KTT"></div>
                                @endif
                            </div>
                            <div class="text-[9px] text-base-content/50 font-medium uppercase tracking-tighter flex justify-between">
                                <span>{{ $contractor_id ? 'Contractor' : 'Internal' }}</span>
                                <span>{{ in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrem']) ? 'KTT' : 'OHS' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS: OTORITAS --}}
            <div class="border shadow-sm stats border-base-300 bg-base-100 sm:col-span-2 lg:col-span-1">
                <div class="p-2 stat">
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
            // Panggil fungsi dari Backend yang sudah kita buat tadi
            $canEdit=$this->getCanEditProperty($i);
            $hasErrorInStep = $this->hasErrorInStep($i);
            $stepTitle = $this->getStepTitle($i);
            $isStepCompleted = $allStepsData['step' . $i] ?? false;
            @endphp

            <div wire:key="step-edit-container-{{ $i }}"
                @class([ 'border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl relative z-0 transition-all duration-300 ease-in-out' , 'hover:-translate-x-1 hover:shadow-xl hover:z-10'=> $canEdit,
                'bg-base-200/50 cursor-not-allowed opacity-80' => !$canEdit,
                'border-error shadow-md' => $hasErrorInStep,
                'hover:border-info' => $canEdit && !$hasErrorInStep
                ])>

                <input type="radio" name="edit-accordion"
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
                        <span class="hidden md:inline">– {{ $stepTitle }}</span>

                        @if(!$canEdit)
                        <x-icon name="lock-closed" class="w-3 h-3 opacity-60" />
                        <span class="text-[9px] lowercase font-normal opacity-70">(akses terbatas)</span>
                        @endif

                        @if($hasErrorInStep)
                        <span class="ml-2 text-white border-none badge badge-sm badge-ghost bg-white/20 animate-pulse text-[9px]">⚠️ ERROR</span>
                        @else
                        <div class="tooltip tooltip-right" data-tip="{{ $isStepCompleted ? 'Bagian Selesai' : ($i == 9 ? 'Membutuhkan komentar OHS/KTT/Vendor' : 'Data belum lengkap') }}">
                            <span @class([ 'px-1 ml-2 text-white border-none badge badge-sm ring-1 shadow-sm' , 'badge-success ring-success/30'=> $isStepCompleted,
                                'badge-error opacity-40 ring-error/30 cursor-help' => !$isStepCompleted
                                ])>
                                {{ $isStepCompleted ? '✓' : '✕' }}
                            </span>
                        </div>
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

                        {{-- Form Field Render --}}
                        @include('livewire.incident.step_edit.incident-step-' . $i, ['readonly' => !$canEdit])

                        {{-- Navigation & Actions --}}
                        <div class="flex justify-between pt-4 mt-4 border-t border-base-200">
                            <div>
                                @if($i > 1)
                                <button type="button" wire:click="goToStep({{ $i - 1 }})" class="btn btn-ghost btn-xs">« Kembali</button>
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
                                        <button type="button" wire:click="update" wire:loading.attr="disabled" class="px-4 text-white shadow-md btn btn-xs btn-success">
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
                                        <span class="mt-1 text-[9px] text-error italic animate-pulse">Otoritas KTT wajib untuk rating {{ $rating_name }}</span>
                                        @elseif(!$canEdit)
                                        <span class="mt-1 text-[9px] text-warning italic">Akses terbatas (HSE Policy)</span>
                                        @endif
                                        @endif
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor

            {{-- Modal Audit Trail --}}
            <dialog class="modal" id="my_modal_2" role="dialog" wire:ignore.self>
                {{-- ... (Konten Modal Audit Trail tetap sama) ... --}}
            </dialog>
    </x-incident.layout>
</section>

@push('scripts')
<script>
    window.addEventListener('scroll-to-top', () => window.scrollTo({
        top: 0,
        behavior: 'smooth'
    }));
</script>
@endpush
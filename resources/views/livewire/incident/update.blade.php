<section class="w-full">
    <x-toast />

    {{-- Breadcrumb khusus Edit --}}
    <div class="flex justify-start mb-2" wire:ignore>
        {{ Breadcrumbs::render('incident-detail', $incidentId) }}
    </div>

    <div class="space-y-2">
        {{-- HEADER SECTION --}}
        <div class="w-full bg-base-200 rounded-full h-1.5 mb-2 overflow-hidden shadow-inner">
            <div class="h-full transition-all duration-700 ease-in-out bg-primary" style="width: {{ $this->getProgressPercentage() }}%"></div>
        </div>
        <button class="btn btn-primary btn-xs" wire:click="showPreview({{ $incidentId }})" onclick="incident_modal.showModal()">
            <span wire:loading wire:target="showPreview" class="loading loading-spinner loading-xs"></span>
            Buka Incident Alert
        </button>

        <dialog id="incident_modal" class="modal" wire:ignore.self>
            <div class="w-11/12 max-w-4xl p-0 overflow-hidden border border-gray-300 modal-box">
                <form method="dialog">
                    <button class="absolute z-10 text-white btn btn-sm btn-circle btn-ghost right-2 top-2">✕</button>
                </form>

                @if($previewData)
                <div class="py-2 text-lg font-bold tracking-wider text-center text-white uppercase bg-red-600">
                    Preliminary Significant Incident Alert
                </div>

                <div class="p-4 text-sm text-black bg-white">
                    <div class="grid grid-cols-12 border-t border-l border-black">
                        <div class="col-span-3 p-2 font-semibold border-b border-r border-black bg-gray-50">Safety Alert No.</div>
                        <div class="col-span-3 p-2 border-b border-r border-black">{{ $previewData['safety_no'] }}</div>
                        <div class="col-span-3 p-2 font-semibold border-b border-r border-black bg-gray-50">INX No.</div>
                        <div class="col-span-3 p-2 border-b border-r border-black">{{ $previewData['inx_no'] }}</div>

                        <div class="col-span-3 p-2 font-semibold border-b border-r border-black bg-gray-50">Tanggal / Date</div>
                        <div class="col-span-3 p-2 border-b border-r border-black">{{ $previewData['date'] }}</div>
                        <div class="col-span-3 p-2 font-semibold border-b border-r border-black bg-gray-50">Waktu / Time</div>
                        <div class="col-span-3 p-2 border-b border-r border-black">{{ $previewData['time'] }}</div>

                        <div class="col-span-3 p-2 text-xs font-semibold border-b border-r border-black bg-gray-50">Lokasi</div>
                        <div class="col-span-3 p-2 border-b border-r border-black">{{ $previewData['location'] }}</div>
                        <div class="col-span-3 p-2 text-xs font-semibold border-b border-r border-black bg-gray-50">Perusahaan/Departemen</div>
                        <div class="col-span-3 p-2 border-b border-r border-black">{{ $previewData['department'] }}</div>
                        <div class="col-span-3 p-2 text-xs font-semibold border-b border-r border-black bg-gray-50">Uraian singkat insiden </div>
                        <div class="col-span-9 p-2 border-b border-r border-black">
                            @php
                            $cleanDescription = strip_tags($previewData['description']);
                            $truncatedDescription = Str::limit($cleanDescription, 50, '...');
                            @endphp
                            {{ $truncatedDescription }}
                        </div>
                        <div class="col-span-3 p-2 text-xs font-semibold border-b border-r border-black bg-gray-50">Tindakan langsung untuk mencegah kejadian serupa terulang </div>
                        <div class="col-span-9 p-2 border-b border-r border-black">
                            @php
                            $cleanimmediate_actions = strip_tags($previewData['immediate_actions']);
                            $truncatedimmediate_actions = Str::limit($cleanimmediate_actions, 50, '...');
                            @endphp
                            {{ $truncatedimmediate_actions }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 p-4 mt-2 border border-black">
                        @for ($i = 0; $i < 2; $i++)
                            @php $photo=$previewData['photos']->values()->get($i); @endphp
                            <div class="flex items-center justify-center h-64 overflow-hidden bg-gray-100 border-t border-b border-l border-r border-black">
                                @if($photo && $photo['exists'])
                                {{-- Mengubah storage_path menjadi URL publik untuk preview browser --}}
                                <img src="{{ asset('storage/' . str_replace(storage_path('app/public/'), '', $photo['full_path'])) }}" class="object-contain w-full h-full">
                                @else
                                <span class="italic text-gray-400">No Photo {{ $i + 1 }}</span>
                                @endif
                            </div>
                            @endfor
                            <div class="p-1 text-xs italic font-bold text-center border-b border-r border-black">Photo 1</div>
                            <div class="p-1 text-xs italic font-bold text-center border-b border-r border-black">Photo 2</div>
                    </div>

                    <div class="mt-2 border-t border-l border-black">
                        <div class="grid grid-cols-12">
                            <div class="col-span-3 p-2 text-xs font-semibold border-b border-r border-black">Approved By</div>
                            <div class="col-span-3 p-2 border-b border-r border-black">{{ $previewData['approver_name'] }}</div>
                            <div class="col-span-3 p-2 font-bold border-b border-r border-black">KTT PT MSM</div>
                            <div class="col-span-3 p-2 border-b border-r border-black">{{ $previewData['approval_date'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="p-4 modal-action bg-gray-50">
                    <form method="dialog">
                        <button class="btn btn-ghost">Tutup</button>
                        <button class="text-white btn btn-error" wire:click="sendAlert">
                            <span wire:loading wire:target="sendAlert" class="loading loading-spinner"></span>
                            Kirim Email
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </dialog>
        {{-- SUMMARY WIDGET SENTRY --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- STATS: NOMOR LAPORAN --}}
            <div class="overflow-visible border shadow-sm stats border-base-300 bg-base-100">
                <div class="p-2 stat">
                    <div class="flex items-center">
                        <div class="grow">
                            <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-base-content/60">Update Laporan Insiden</div>
                            <flux:subheading size="sm" class="flex items-center gap-2 text-accent">
                                {{ __('Nomor Laporan:') }}
                                <span class="font-black tracking-tight text-primary">{{ $report_number }}</span>
                            </flux:subheading>
                        </div>
                        <div class="grow-0">
                            <flux:button size="xs" variant="accent" icon='clock' onclick="my_modal_2.showModal()"></flux:button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS: STATUS --}}
            <div class="overflow-visible border shadow-sm stats border-base-300 bg-base-100">
                <div class="p-2 stat">
                    <div class="flex items-center">
                        <div class="flex flex-col">
                            <div class="stat-title text-[10px] uppercase font-bold tracking-tighter text-base-content/60">Status Laporan</div>
                            <div class="flex items-center gap-2 mt-1 stat-value">

                                @switch($this->mainStatus)
                                @case('Open') @case('Reported')
                                <div aria-label="error" class="status status-error animate-pulse"></div>
                                <span class="text-xs italic font-black uppercase text-error">OPEN / REPORTED</span>
                                @break

                                @case('In Progress')
                                <div class="flex items-center gap-2">
                                    <div class="status status-warning animate-bounce"></div>
                                    <span class="text-xs italic font-black uppercase text-warning">IN PROGRESS</span>

                                    @if($this->subStatus)
                                    <div class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-warning/10 border border-warning/20">
                                        <span class="text-[9px] font-bold text-info uppercase tracking-widest">
                                            HAS {{ $this->subStatus }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                @break

                                {{-- TAMBAHAN: WAITING REVIEW --}}
                                @case('Waiting Review')
                                <div class="flex items-center gap-2">
                                    <div class="inline-grid *:[grid-area:1/1]">
                                        <div class="status status-info animate-ping"></div>
                                        <div class="status status-info"></div>
                                    </div>
                                    <span class="text-xs italic font-black uppercase text-info">WAITING REVIEW</span>
                                    <div class="px-2 badge badge-soft badge-info text-[9px] font-bold uppercase ">
                                        <span class="text-[9px] font-bold ">A waiting Signatures</span>
                                    </div>
                                </div>
                                @break

                                @case('Action Required')
                                <div aria-label="status" class="status status-neutral"></div>

                                <span class="text-xs italic font-black uppercase text-neutral">ACTION REQUIRED</span>
                                @break

                                @case('Closed')
                                <div aria-label="success" class="status status-success"></div>
                                <span class="text-xs italic font-black uppercase text-success">CLOSED</span>

                                @can('reviewReport', $incident)
                                <button
                                    wire:click="reopen"
                                    wire:confirm="Apakah Anda yakin ingin membuka kembali laporan ini untuk perbaikan data?"
                                    class="ml-4 btn btn-outline btn-error btn-xs">
                                    <x-icon name="arrow-path" class="w-3 h-3" />
                                    Re-open Report
                                </button>
                                @endcan
                                @break
                                @endswitch

                            </div>
                        </div>
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
                                    <span
                                        class="flex items-center justify-center w-full h-full rounded-lg text-base-content bg-base-200 ">{{ substr($incident->latest_reviewer_name ?? 'N', 0, 1) }}
                                    </span>
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

        <div class="w-full overflow-x-auto">
            <div class="tabs tabs-lift tabs-lg min-w-max">

                @for ($i = 1; $i <= 9; $i++)
                    @php
                    // Panggil fungsi dari Backend yang sudah kita buat tadi
                    $canEdit=$this->getCanEditProperty($i);
                    $hasErrorInStep = $this->hasErrorInStep($i);
                    $stepTitle = $this->getStepTitle($i);
                    $isStepCompleted = $allStepsData['step' . $i] ?? false;

                    // Logika Baru: Cek apakah step 1 sampai 8 sudah berstatus true
                    $allPreviousStepsCompleted = true;

                    for ($prev = 1; $prev
                    <= 8; $prev++) {
                        if (!($allStepsData['step' . $prev] ?? false)) {
                        $allPreviousStepsCompleted=false;
                        break;
                        }
                        }
                        @endphp

                        @if ($i==9 && !$allPreviousStepsCompleted)
                        @continue
                        @endif

                        {{-- TAB HEADER --}}
                        <input
                        type="radio"
                        name="edit-tabs"
                        class="tab text-[11px] font-bold uppercase"
                        aria-label="Bagian {{ $i }}"
                        wire:click="{{ $canEdit ? "goToStep($i)" : "" }}"
                        value="{{ $i }}"
                        {{ $currentStep == $i ? 'checked' : '' }}
                        {{ !$canEdit ? 'disabled' : '' }} />

                    {{-- TAB CONTENT --}}
                    <div
                        wire:key="step-edit-container-{{ $i }}"
                        @class([ 'tab-content border border-base-300 bg-base-100 rounded-box p-4 transition-all duration-300 ease-in-out' , 'border-error shadow-md'=> $hasErrorInStep,
                        'opacity-80 bg-base-200/30' => !$canEdit,
                        ])>

                        {{-- HEADER --}}
                        <div
                            @class([ 'flex items-center justify-between mb-4 rounded-lg px-4 py-3 transition-colors duration-300' , 'bg-error text-error-content'=> $hasErrorInStep,
                            'bg-gradient-to-r from-accent to-info text-white' => $currentStep == $i && !$hasErrorInStep,
                            'bg-base-200 text-base-content' => $currentStep != $i && $canEdit,
                            'bg-base-300 text-base-content/40' => !$canEdit,
                            ])>

                            <h3 class="flex items-center gap-2 text-xs font-bold tracking-wide uppercase">
                                <span>{{ __('BAGIAN') }} {{ $i }}</span>

                                <span class="hidden md:inline">
                                    – {{ __($stepTitle) }}
                                </span>

                                @if(!$canEdit)
                                <x-icon name="lock-closed" class="w-3 h-3 opacity-60" />
                                <span class="text-[9px] lowercase font-normal opacity-70">
                                    (akses terbatas)
                                </span>
                                @endif

                                @if($hasErrorInStep)
                                <span class="ml-2 text-white border-none badge badge-sm badge-ghost bg-white/20 animate-pulse text-[9px]">
                                    ⚠️ ERROR
                                </span>
                                @else
                                <div
                                    class="tooltip tooltip-right"
                                    data-tip="{{ $isStepCompleted ? 'Bagian Selesai' : ($i == 9 ? __('Membutuhkan komentar OHS/KTT/Vendor') : __('Data belum lengkap')) }}">

                                    <span
                                        @class([ 'px-1 ml-2 text-white border-none badge badge-sm ring-1 shadow-sm' , 'badge-success ring-success/30'=> $isStepCompleted,
                                        'badge-error opacity-40 ring-error/30 cursor-help' => !$isStepCompleted
                                        ])>

                                        {{ $isStepCompleted ? '✓' : '✕' }}
                                    </span>
                                </div>
                                @endif
                            </h3>
                        </div>

                        {{-- CONTENT --}}
                        <div class="text-xs">

                            @if($hasErrorInStep)
                            <div class="p-2 mb-4 text-xs border rounded-lg bg-error/10 text-error border-error/20">
                                <strong>{{ __('Perhatian:') }}</strong>
                                {{ __('Beberapa kolom pada bagian ini memerlukan perbaikan sebelum laporan dapat diperbarui.') }}
                            </div>
                            @endif

                            {{-- Form Field Render --}}
                            @include(
                            'livewire.incident.step_edit.incident-step-' . $i,
                            ['readonly' => !$canEdit]
                            )

                            {{-- Navigation & Actions --}}
                            <div class="flex justify-between pt-4 mt-4 border-t border-base-200">

                                <div>
                                    @if($i > 1)
                                    <button
                                        type="button"
                                        wire:click="goToStep({{ $i - 1 }})"
                                        class="btn btn-ghost btn-xs">

                                        {{ __('« Kembali') }}
                                    </button>
                                    @endif
                                </div>

                                <div class="flex gap-2">

                                    {{-- NAVIGATION --}}
                                    @if ($i < 9)
                                        <flux:button
                                        type="button"
                                        variant="info"
                                        size="xs"
                                        wire:click="nextStep"
                                        :disabled="false"
                                        wire:loading.attr="disabled"
                                        loading.target="nextStep"
                                        class="px-4 shadow-sm">

                                        <span wire:loading.remove wire:target="nextStep">
                                            {{ $canEdit ? __('Simpan & Lanjut »') : __('Lihat Selanjutnya »') }}
                                        </span>
                                        </flux:button>
                                        @endif

                                        <div class="flex flex-col items-end">

                                            {{-- UPDATE --}}
                                            @if($this->canUpdate)

                                            <flux:button
                                                type="button"
                                                wire:click="update"
                                                variant="primary"
                                                size="xs"
                                                :disabled="!$canEdit"
                                                wire:loading.attr="disabled"
                                                loading.target="update, visual_evidence, supporting_documents"
                                                class="px-4 shadow-md">

                                                <span wire:loading.remove wire:target="update, visual_evidence, supporting_documents">
                                                    {{ __('Update Laporan') }}
                                                </span>

                                                <span
                                                    wire:loading.remove.class="hidden"
                                                    wire:target="update, visual_evidence, supporting_documents"
                                                    class="flex items-center hidden gap-2">

                                                    {{ __('Proses Update...') }}

                                                    <span
                                                        wire:loading.remove.class="hidden"
                                                        class="hidden loading loading-spinner loading-xs">
                                                    </span>
                                                </span>
                                            </flux:button>

                                            @else

                                            {{-- LOCKED --}}
                                            <button
                                                disabled
                                                class="px-4 opacity-50 btn btn-xs btn-disabled bg-base-300">

                                                <div class="flex items-center gap-1">
                                                    <x-icon name="lock-closed" class="w-3 h-3" />
                                                    <span>{{ __('Update Terkunci') }}</span>
                                                </div>
                                            </button>

                                            @endif

                                            {{-- NOTES --}}
                                            @if($i == 9 && in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrem']) && empty($penerimaan_komentar_ktt_id))
                                            <span class="mt-1 text-[9px] text-error italic animate-pulse">
                                                * Otoritas KTT wajib untuk rating {{ $rating_name }}
                                            </span>
                                            @endif

                                            @if(!$canEdit)
                                            <span class="mt-1 text-[9px] text-warning italic">
                                                Akses terbatas (HSE Policy)
                                            </span>
                                            @endif

                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @endfor
            </div>
        </div>
</section>

@push('scripts')
<script>
    window.addEventListener('scroll-to-top', () => window.scrollTo({
        top: 0,
        behavior: 'smooth'
    }));
</script>
@endpush
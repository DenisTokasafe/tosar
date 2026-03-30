<section class="w-full">
    <x-toast />

    {{-- Breadcrumb khusus Edit --}}
    <div class="flex justify-start mb-2" wire:ignore>
        {{ Breadcrumbs::render('incident-detail', $incidentId) }}
    </div>

    <div class="space-y-6">
        {{-- HEADER SECTION --}}

        {{-- TOTAL PROGRESS BAR (Optional but Recommended) --}}
        <div class="w-full bg-base-200 rounded-full h-1.5 mb-2 overflow-hidden shadow-inner">
            <div class="h-full transition-all duration-700 ease-in-out bg-primary" style="width: {{ $this->getProgressPercentage() }}%"></div>
        </div>

        {{-- SUMMARY WIDGET SENTRY --}}
        {{-- Responsive: 1 kolom di HP, 2 kolom di Tablet, 3 kolom di Desktop --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- STATS: STATUS --}}
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
                                @if($contractor_id)
                                <span>Contractor</span>
                                @else
                                <span>Internal</span>
                                @endif
                                @if(in_array($rating_name, ['Sedang', 'Tinggi', 'Ekstrem']))
                                <span>KTT</span>
                                @else
                                <span>OHS</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS: OTORITAS (Full width di Tablet agar seimbang) --}}
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

                    {{-- Ganti bagian h3 di dalam collapse-title dengan ini --}}
                    <h3 class="flex items-center gap-2 text-xs font-bold tracking-wide uppercase">
                        <span>BAGIAN {{ $i }}</span>
                        <span class="hidden md:inline">– {{ $stepTitles[$i] }}</span>

                        @if(!$canEdit)
                        <x-icon name="lock-closed" class="w-3 h-3 opacity-60" />
                        <span class="text-[9px] lowercase font-normal opacity-70">(akses terbatas)</span>
                        @endif

                        @if($hasErrorInStep)
                        {{-- Status Error jika validasi gagal --}}
                        <span class="ml-2 text-white border-none badge badge-sm badge-ghost bg-white/20 animate-pulse text-[9px]">⚠️ ERROR</span>
                        @else
                        @php
                        // Membaca status penyelesaian dari array allStepsData
                        $isStepCompleted = $allStepsData['step' . $i] ?? false;

                        // Tooltip khusus untuk Step 9 agar lebih informatif
                        $tooltipTip = ($i == 9)
                        ? "Membutuhkan komentar OHS/KTT/Vendor"
                        : "Data bagian ini belum lengkap";
                        @endphp

                        @if($isStepCompleted)
                        {{-- Centang Hijau jika data sudah ada --}}
                        <div class="tooltip tooltip-right" data-tip="Bagian Selesai">
                            <span class="px-1 ml-2 text-white border-none badge badge-sm badge-success ring-1 ring-success/30 shadow-sm">
                                ✓
                            </span>
                        </div>
                        @else
                        {{-- Tanda X jika data belum diinput --}}
                        <div class="tooltip tooltip-right" data-tip="{{ $tooltipTip }}">
                            <span class="px-1 ml-2 text-white border-none badge badge-sm badge-error opacity-40 ring-1 ring-error/30 cursor-help transition-opacity hover:opacity-100">
                                ✕
                            </span>
                        </div>
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


            {{-- Modal DaisyUI --}}
            <dialog class="modal" id="my_modal_2" role="dialog" wire:ignore.self>
                <div class="md:max-w-5xl modal-box">
                    <form method="dialog">
                        <button class="absolute btn btn-sm btn-circle btn-ghost right-2 top-2">✕</button>
                    </form>
                    <h3 class="flex items-center gap-2 mb-4 text-lg font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Audit Trail System - {{ $this->incident->incident_number ?? 'Draft' }}
                    </h3>

                    <div class="max-h-[75vh] overflow-y-auto overflow-x-auto border rounded-lg">
                        <table class="table border table-xs table-pin-rows">
                            <thead>
                                <tr class="bg-base-200 text-base-content">
                                    <th class="w-32 px-2 py-2 text-center border">{{ __('Waktu') }}</th>
                                    <th class="px-2 py-2 border w-44">{{ __('User & Modul') }}</th>
                                    <th class="px-2 py-2 border">{{ __('Detail Perubahan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $allLogs = (isset($this->incident) && $this->incident->exists)
                                ? $this->incident->allActivities()->latest()->get()
                                : collect();
                                @endphp

                                @forelse($allLogs as $activity)
                                @php
                                $subjectName = class_basename($activity->subject_type);
                                $attributes = $activity->properties['attributes'] ?? [];
                                $old = $activity->properties['old'] ?? [];

                                // Deteksi Identitas Subjek (Penting untuk Step 2 & Relasi Lain)
                                $summary = $attributes['person_name'] ??
                                ($attributes['action_summary'] ??
                                ($attributes['timeline_summary'] ??
                                ($attributes['file_display'] ??
                                ($attributes['peepo_category'] ??
                                ($attributes['impact_summary'] ?? '')))));
                                @endphp
                                <tr class="hover">
                                    {{-- KOLOM 1: WAKTU --}}
                                    <td class="px-2 py-2 border align-top font-mono text-[10px] text-center">
                                        <div class="font-bold text-base-content">{{ $activity->created_at->format('d/m/Y') }}</div>
                                        <div class="italic opacity-50">{{ $activity->created_at->format('H:i:s') }}</div>
                                    </td>

                                    {{-- KOLOM 2: USER & MODUL --}}
                                    <td class="px-2 py-2 align-top border">
                                        <div class="w-40 text-xs font-bold truncate text-primary" title="{{ $activity->causer->name ?? 'System' }}">
                                            {{ $activity->causer->name ?? 'System' }}
                                        </div>
                                        <div class="flex items-center gap-1 mt-1">
                                            {{-- Badge dinamis untuk membedakan tipe subjek --}}
                                            <span class="badge {{ $subjectName == 'InvolvedPerson' ? 'badge-info' : 'badge-ghost' }} badge-xs text-[9px] px-1.5 uppercase font-black tracking-tighter">
                                                {{ $subjectName == 'InvolvedPerson' ? 'PERSONNEL' : $subjectName }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- KOLOM 3: DETAIL PERUBAHAN --}}
                                    <td class="px-2 py-2 border text-wrap">
                                        {{-- Judul Event (Created/Updated/Deleted) & Identitas Objek --}}
                                        <div class="flex items-center flex-wrap gap-1.5 mb-2">
                                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded {{ $activity->description == 'deleted' ? 'bg-error text-error-content' : 'bg-base-300 text-base-content' }} uppercase">
                                                {{ $activity->description }}
                                            </span>
                                            @if($summary)
                                            <span class="text-[10px] font-bold text-secondary italic">
                                                "{{ $summary }}"
                                            </span>
                                            @endif
                                        </div>

                                        {{-- Grid Perubahan Field --}}
                                        <div class="grid grid-cols-1 gap-1">
                                            @foreach ($attributes as $field => $newValue)
                                            {{-- Skip meta fields dan field ringkasan agar tidak duplikat --}}
                                            @continue(in_array($field, ['updated_at', 'created_at', 'incident_report_id', 'id']) ||
                                            str_ends_with($field, '_label') ||
                                            in_array($field, ['person_name', 'action_summary', 'timeline_summary', 'file_display', 'peepo_category', 'impact_summary', 'person_nik']))

                                            @php
                                            // Gunakan label hasil tapActivity (format manusiawi)
                                            $displayOld = $old[$field . '_label'] ?? ($old[$field] ?? '-');
                                            $displayNew = $attributes[$field . '_label'] ?? ($newValue ?? '-');

                                            // Bersihkan nama label (misal: 'employee_name' jadi 'Employee name')
                                            $label = ucfirst(str_replace(['_id', '_'], ['', ' '], $field));
                                            @endphp

                                            <div class="flex flex-col p-1.5 bg-base-200/40 rounded border border-base-300/50">
                                                <span class="font-bold text-gray-500 uppercase text-[8px] leading-none mb-1">{{ $label }}</span>
                                                <div class="flex items-center gap-2 text-[11px] leading-tight">
                                                    @if($activity->description === 'updated')
                                                    <div class="px-1 line-through break-all rounded opacity-50 bg-error/5 text-error">
                                                        {{ is_array($displayOld) ? json_encode($displayOld) : $displayOld }}
                                                    </div>
                                                    <span class="text-[10px] opacity-30">→</span>
                                                    @endif

                                                    <div class="px-1 font-semibold break-all rounded bg-success/10 text-success">
                                                        {{ is_array($displayNew) ? json_encode($displayNew) : $displayNew }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center opacity-20">

                                            <span class="text-sm font-bold tracking-widest uppercase">Belum ada riwayat perubahan</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-action">
                        <form method="dialog">
                            <button class="btn btn-sm">Tutup</button>
                        </form>
                    </div>
                </div>
            </dialog>
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
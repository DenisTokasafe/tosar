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
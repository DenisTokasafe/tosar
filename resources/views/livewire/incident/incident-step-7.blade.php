{{-- SECTION DOKUMENTASI --}}
<fieldset class="p-3 mt-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title ">{{ __('Dokumentasi') }}</legend>
    <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-2">

        {{-- Bukti Visual --}}
        <fieldset class="fieldset">
            <x-form.upload label="Lampirkan Bukti Visual" model="visual_evidence" title="Pilih Gambar"
                keterangan="Bisa pilih lebih dari 1 foto (JPG, PNG)" :file="$visual_evidence" multiple /> {{-- Tambah multiple --}}

            <div wire:loading.remove wire:target="visual_evidence" class="flex flex-wrap gap-2 mt-2">
                @if($visual_evidence)
                @foreach($visual_evidence as $index => $image)
                <div class="relative group">
                    @php
                    // Ambil ekstensi file secara aman
                    $extension = strtolower($image->getClientOriginalExtension());
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp

                    @if($isImage)
                    {{-- Hanya panggil temporaryUrl jika file adalah gambar --}}
                    <img src="{{ $image->temporaryUrl() }}" class="object-cover w-24 h-24 border rounded shadow-sm" />
                    @else
                    {{-- Tampilkan icon dokumen jika bukan gambar --}}
                    <div class="flex flex-col items-center justify-center w-24 h-24 border rounded bg-base-200">
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" />
                        </svg>
                        <span class="text-[10px] px-1 truncate w-full text-center">{{ $image->getClientOriginalName() }}</span>
                    </div>
                    @endif

                    <button type="button" wire:click="removeFile('visual_evidence', {{ $index }})"
                        class="absolute flex items-center justify-center w-5 h-5 text-white rounded-full -top-2 -right-2 bg-error">✕</button>
                </div>
                @endforeach
                @endif
            </div>
            <x-label-error :messages="$errors->get('visual_evidence.*')" />
        </fieldset>

        {{-- Dokumen Pendukung --}}
        <fieldset class="fieldset">
            <x-form.upload label="Lampirkan Dokumen Pendukung" model="supporting_documents" title="Pilih Dokumen"
                keterangan="Bisa pilih lebih dari 1 dokumen (PDF, Word)" :file="$supporting_documents" multiple />

            <div wire:loading.remove wire:target="supporting_documents" class="mt-2 space-y-2">
                @if($supporting_documents)
                @foreach($supporting_documents as $index => $doc)
                @php $docExt = strtolower($doc->getClientOriginalExtension()); @endphp
                <div class="flex items-center justify-between p-2 border border-dashed rounded border-base-300">
                    <div class="flex items-center gap-2">
                        @if($docExt == 'pdf') <x-icon.pdf class="w-6 h-6" />
                        @elseif(in_array($docExt, ['doc', 'docx'])) <x-icon.word class="w-6 h-6" />
                        @else <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                        </svg>
                        @endif
                        <span class="w-40 text-xs font-medium truncate">{{ $doc->getClientOriginalName() }}</span>
                    </div>
                    <button type="button" wire:click="removeFile('supporting_documents', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                </div>
                @endforeach
                @endif
            </div>
            <x-label-error :messages="$errors->get('supporting_documents.*')" />
        </fieldset>
    </div>
</fieldset>

{{-- SECTION TINDAKAN PERBAIKAN --}}
<fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title ">{{ __('Tindakan Perbaikan') }}</legend>

    <div class="flex items-center justify-between pb-2 mb-4 border-b">
        <h3 class="text-sm font-bold uppercase">{{ __('Rencana Perbaikan Jangka Panjang') }}</h3>
        <button type="button" wire:click="addCorrectiveRow" class="btn btn-primary btn-xs">
            + {{ __('Tambah Rencana') }}
        </button>
    </div>


    <div class="overflow-x-auto">
        <table class="table w-full table-compact">
            <thead>
                <tr class="text-xs">
                    <th>Rencana Perbaikan</th>
                    <th>Kontrol Hirarki</th>
                    <th>PIC</th>
                    <th>Batas Waktu</th>
                    <th>Tgl. Selesai</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($corrective_actions as $index => $action)
                <tr wire:key="corrective-row-{{ $index }}">
                    <td class="w-1/4">
                        <x-form.text_area
                            model="corrective_actions.{{ $index }}.action_description"
                            placeholder="{{ __('Langkah agar tidak terulang...') }}"
                            rows="2" />
                    </td>
                    <td class="w-1/4">
                        <x-form.select
                            model="corrective_actions.{{ $index }}.control_hierarchy"
                            :options="[
                                        ['id' => 'Eliminasi', 'name' => 'Eliminasi'],
                                        ['id' => 'Substitusi', 'name' => 'Substitusi'],
                                        ['id' => 'Engineering', 'name' => 'Rekayasa Teknik'],
                                        ['id' => 'Administrasi', 'name' => 'Administrasi'],
                                        ['id' => 'APD', 'name' => 'APD'],
                                    ]"
                            placeholder="Pilih Hirarki..." />
                    </td>
                    <td class="w-1/6">
                        <x-form.searchable-select-advanced
                            placeholder="Cari nama..."
                            modelsearch="searchPetugas.{{ $index }}"
                            modelid="corrective_actions.{{ $index }}.name" :options="$pelaporsAct"
                            :showdropdown="$showDropdownPetugas[$index] ?? false" :manualMode="$manualActPelaporMode" clickaction="selectActPelapor" />
                    </td>
                    <td class="w-1/6 text-xs">
                        <x-form.tgl-waktu
                            model="corrective_actions.{{ $index }}.due_date"
                            :min-date="now()->format('Y-m-d')" />
                    </td>
                    <td class="w-1/6 text-xs">
                        <x-form.tgl-waktu
                            model="corrective_actions.{{ $index }}.actual_completion_date"
                            :min-date="now()->format('Y-m-d')" />
                    </td>
                    <td class="w-auto">
                        @if(count($corrective_actions) > 1)
                        <button type="button" wire:click="removeCorrectiveRow({{ $index }})"
                            class="btn btn-ghost btn-xs text-error">✕</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</fieldset>
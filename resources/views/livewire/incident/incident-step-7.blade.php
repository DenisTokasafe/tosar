{{-- SECTION DOKUMENTASI --}}
<fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title ">{{ __('Dokumentasi') }}</legend>
    <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-2">

        {{-- Bukti Visual --}}
        <fieldset class="fieldset">
            <x-form.upload label="Lampirkan Bukti Visual" model="visual_evidence" title="Pilih Gambar"
                keterangan="Pilih Bukti Visual: Foto" :file="$visual_evidence" />

            <div wire:loading.remove wire:target="visual_evidence">
                @if($visual_evidence)
                @php $extension = strtolower($visual_evidence->getClientOriginalExtension()); @endphp

                @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                {{-- Preview Gambar --}}
                <img src="{{ $visual_evidence->temporaryUrl() }}" class="w-40 h-auto mt-2 border rounded shadow-sm" />
                @else
                {{-- Fallback jika format lain --}}
                <div class="flex items-center gap-2 p-2 mt-2 rounded bg-base-200">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm">File: {{ $visual_evidence->getClientOriginalName() }}</span>
                </div>
                @endif
                @endif
            </div>
            <x-label-error :messages="$errors->get('visual_evidence')" />
        </fieldset>

        {{-- Dokumen Pendukung --}}
        <fieldset class="fieldset">
            <x-form.upload label="Lampirkan Dokumen Pendukung" model="supporting_documents" title="Pilih Dokumen"
                keterangan="Pilih Dokumen Pendukung: Word, PDF" :file="$supporting_documents" />

            <div wire:loading.remove wire:target="supporting_documents">
                @if($supporting_documents)
                @php $docExt = strtolower($supporting_documents->getClientOriginalExtension()); @endphp

                <div class="flex items-center gap-2 p-2 mt-2 border border-dashed rounded border-base-300">
                    @if($docExt == 'pdf')
                    <x-icon.pdf class="w-8 h-8" />
                    <span class="text-sm font-medium text-red-600">{{ $supporting_documents->getClientOriginalName() }}</span>
                    @elseif(in_array($docExt, ['doc', 'docx']))
                    <x-icon.word class="w-8 h-8" />
                    <span class="text-sm font-medium text-blue-600">{{ $supporting_documents->getClientOriginalName() }}</span>
                    @else
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                    </svg>
                    <span class="text-sm text-gray-600">{{ $supporting_documents->getClientOriginalName() }}</span>
                    @endif
                </div>
                @endif
            </div>
            <x-label-error :messages="$errors->get('supporting_documents')" />
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

    <div class="mb-4">
        <x-form.text_area label="Tindakan Langsung (Emergency Action)" model="emergency_action"
            placeholder="{{ __('Jelaskan tindakan segera...') }}"
            readonly /> {{-- Gunakan readonly jika hanya tampil, disabled terkadang tidak mengirim data --}}
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full table-compact">
            <thead>
                <tr class="text-xs">
                    <th>Rencana Perbaikan</th>
                    <th>PIC</th>
                    <th>Batas Waktu</th>
                    <th>Tgl. Selesai</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($corrective_actions as $index => $action)
                <tr wire:key="corrective-row-{{ $index }}">
                    <td class="w-1/3">
                        <x-form.text_area
                            model="corrective_actions.{{ $index }}.action_description"
                            placeholder="{{ __('Langkah agar tidak terulang...') }}"
                            rows="2" />
                    </td>
                    <td class="w-1/4">
                        <x-form.searchable-select-advanced :disabled="$isDisabled"
                            label="Petugas Inspeksi {{ $index + 1 }}"
                            placeholder="Cari nama..."
                            modelsearch="searchPetugas.{{ $index }}"
                            modelid="inspectors.{{ $index }}.name" :options="$pelaporsAct"
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
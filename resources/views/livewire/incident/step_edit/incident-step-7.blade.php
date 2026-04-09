{{-- SECTION DOKUMENTASI --}}
<fieldset class="p-3 mt-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title">{{ __('Dokumentasi') }}</legend>
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">

        {{-- 1. VISUAL EVIDENCE --}}
        <div class="p-4 border shadow-sm rounded-xl bg-base-100 border-base-300">
            <x-form.upload label="Visual Evidence" model="visual_evidence" multiple keterangan="JPG, PNG (Max 2MB)" required="true" :disabled="!$canEdit" />

            <div class="grid grid-cols-3 gap-2 mt-3">
                {{-- DATA DARI DATABASE (EXISTING) --}}
                @foreach($existing_visual_evidence as $media)
                <div class="avatar">
                    <div class="relative w-40 rounded bg-warning/10 border border-warning">
                        @php
                        // Enkripsi path untuk masking URL gambar
                        $secureImgUrl = route('document.secure-view', ['path' => Crypt::encryptString($media->file_path)]);
                        @endphp

                        {{-- Gunakan link terenkripsi untuk src gambar --}}
                        <img src="{{ $secureImgUrl }}" class="object-cover w-full h-full border rounded-lg opacity-70" />

                        <div class="absolute inset-0 flex items-center justify-center rounded-lg bg-black/20">
                            <span class="text-[8px] font-bold text-white bg-success px-1 rounded">SAVED</span>
                        </div>
                        <div class="absolute inset-x-0 bottom-0 flex items-center justify-center rounded-lg bg-black/20">
                            <span class="text-[8px] font-bold text-white bg-success px-1 rounded">
                                <a href="{{ $secureImgUrl }}" target="_blank"
                                    class="text-[10px] font-bold text-blue-700 hover:underline truncate">
                                    {{ __('lihat') }}
                                </a>
                            </span>
                        </div>

                        @if($canEdit)
                        <button type="button" wire:click="deleteMedia({{ $media->id }})" wire:confirm="Hapus foto permanen?"
                            class="absolute scale-75 -top-1 -right-1 btn btn-circle btn-error btn-xs">✕</button>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- DATA TEMPORARY (NEW UPLOAD) --}}
                @if($visual_evidence)
                @foreach($visual_evidence as $index => $image)
                <div class="avatar">
                    <div class="relative w-40 rounded">
                        {{-- Untuk file baru yang belum di-save, tetap gunakan temporaryUrl() bawaan Livewire --}}
                        <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full border-2 rounded-lg shadow-md border-primary" />
                        @if($canEdit)
                        <button type="button" wire:click="removeFile('visual_evidence', {{ $index }})"
                            class="absolute scale-75 -top-1 -right-1 btn btn-circle btn-primary btn-xs">✕</button>
                        @endif
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- 2. SUPPORTING DOCUMENTS --}}
        <div class="p-4 border shadow-sm rounded-xl bg-base-100 border-base-300">
            <x-form.upload label="Supporting Docs" model="supporting_documents" multiple keterangan="PDF, DOCX" required="true" :disabled="!$canEdit" />

            <div class="mt-3 space-y-2">
                {{-- DATA DARI DATABASE (EXISTING) --}}
                @foreach($existing_supporting_documents as $doc)
                <div class="flex items-center justify-between p-2 border rounded-lg bg-base-300 border-base-100 group">
                    <div class="flex items-center gap-2 overflow-hidden">
                        @php
                        $ext = pathinfo($doc->file_name, PATHINFO_EXTENSION);
                        // Enkripsi path untuk masking URL dokumen
                        $secureDocUrl = route('document.secure-view', ['path' => Crypt::encryptString($doc->file_path)]);
                        @endphp

                        @if($ext === 'pdf')
                        <x-icon.pdf class="flex-shrink-0 w-4 h-4 text-red-500" />
                        @elseif(in_array($ext, ['doc', 'docx']))
                        <x-icon.word class="flex-shrink-0 w-4 h-4 text-blue-600" />
                        @else
                        <x-icon.document class="flex-shrink-0 w-4 h-4 text-blue-500" />
                        @endif

                        <div class="flex flex-col min-w-0">
                            {{-- Link sekarang menggunakan secure URL --}}
                            <a href="{{ $secureDocUrl }}" target="_blank"
                                class="text-[10px] font-bold text-blue-700 hover:underline truncate">
                                {{ $doc->file_name }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <span class="badge badge-success badge-xs text-[8px] font-bold">SAVED</span>
                        @if($canEdit)
                        <button type="button" wire:click="deleteFileFromDb({{ $doc->id }})" wire:confirm="Hapus permanen?"
                            class="w-5 h-5 min-h-0 p-0 btn btn-ghost btn-xs text-error">✕</button>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- DATA TEMPORARY (NEW UPLOAD) --}}
                @if($supporting_documents)
                @foreach($supporting_documents as $index => $doc)
                <div class="flex items-center justify-between p-2 border border-dashed rounded-lg bg-base-200 border-base-300">
                    <div class="flex items-center gap-2 overflow-hidden opacity-75">
                        <x-icon.document class="flex-shrink-0 w-4 h-4 text-gray-500" />
                        <span class="text-[10px] truncate italic">{{ $doc->getClientOriginalName() }}</span>
                    </div>
                    @if($canEdit)
                    <button type="button" wire:click="removeFile('supporting_documents', {{ $index }})"
                        class="w-5 h-5 min-h-0 p-0 btn btn-ghost btn-xs text-error">✕</button>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</fieldset>

{{-- SECTION TINDAKAN PERBAIKAN --}}
<fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title">{{ __('Rencana Perbaikan Jangka Panjang') }}</legend>

    <div class="flex items-center justify-end pb-2 mb-4 border-b">
        @if($canEdit)
        <x-button.btn-tooltip color="primary" icon="add" wireClick="addCorrectiveRow" tooltip="Tambah" position="top md:right" />
        @else
        <div class="gap-1 italic badge badge-ghost badge-sm opacity-70">
            <x-icon name="lock" class="w-3 h-3" /> Terkunci
        </div>
        @endif
    </div>

    {{-- VIEW DESKTOP --}}
    <div class="hidden overflow-x-auto md:block">
        <table class="table w-full table-compact">
            <thead>
                <tr class="text-[11px] uppercase bg-base-200">
                    <th class="rounded-l-lg">Rencana Perbaikan</th>
                    <th>Hirarki</th>
                    <th>PIC</th>
                    <th>Batas Waktu</th>
                    <th>Tgl. Selesai</th>
                    <th class="text-center rounded-r-lg">
                        @if(!$canEdit) <x-icon name="lock" class="w-3 h-3 mx-auto opacity-40" /> @endif
                    </th>
                </tr>
            </thead>
            <tbody class="text-xs">
                @foreach($corrective_actions as $index => $action)
                <tr wire:key="corrective-desktop-{{ $index }}-{{ count($corrective_actions) }}" class="hover:bg-base-50">
                    <td class="w-1/4 align-top">
                        <x-form.text_area model="corrective_actions.{{ $index }}.action_description" rows="2" :disabled="!$canEdit" />
                    </td>
                    <td class="w-1/5 align-top">
                        <x-form.select model="corrective_actions.{{ $index }}.control_hierarchy"
                            :options="[['id'=>'Eliminasi','name'=>'Eliminasi'],['id'=>'Substitusi','name'=>'Substitusi'],['id'=>'Engineering','name'=>'Rekayasa'],['id'=>'Administrasi','name'=>'Admin'],['id'=>'APD','name'=>'APD']]"
                            :disabled="!$canEdit" />
                    </td>
                    <td class="w-1/5 align-top">
                        <x-form.searchable-select-advanced modelsearch="searchPetugas.{{ $index }}"
                            modelid="corrective_actions.{{ $index }}.pic_user_id" :options="$pelaporsAct"
                            :showdropdown="($showDropdownPetugas[$index] ?? false) && $canEdit"
                            clickaction="selectActPelapor"
                            :disabled="!$canEdit"
                            {{-- Tambahkan Properti Manual di bawah ini --}}
                            :manualMode="$manualModePetugas[$index] ?? false"
                            manualModelName="manualNamePetugas.{{ $index }}"
                            enableManualAction="enableManualPetugas({{ $index }})"
                            addManualAction="addManualPetugas({{ $index }})" />
                    </td>
                    <td class="align-top">
                        <x-form.tgl-waktu model="corrective_actions.{{ $index }}.due_date" :disabled="!$canEdit" />
                    </td>
                    <td class="align-top">
                        <x-form.tgl-waktu model="corrective_actions.{{ $index }}.actual_completion_date" :disabled="!$canEdit" />
                        <div class="flex gap-1 mt-1">
                            @if(!empty($action['due_date']) && !empty($action['actual_completion_date']))
                            @php $isOverdue = \Carbon\Carbon::parse($action['actual_completion_date'])->greaterThan(\Carbon\Carbon::parse($action['due_date'])); @endphp
                            <div class="tooltip" data-tip="{{ $isOverdue ? 'Overdue' : 'Tepat Waktu' }}">
                                <span class="{{ $isOverdue ? 'text-error' : 'text-success' }}">
                                    @if($isOverdue) ⚠️ @else ✅ @endif
                                </span>
                            </div>
                            @endif
                            @if(!empty($action['actual_completion_date']))
                            <span class="badge badge-success badge-outline text-[8px] h-3 px-1">DONE</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center align-top">
                        @if($canEdit && count($corrective_actions) > 1)
                        <button type="button" wire:click="removeCorrectiveRow({{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</fieldset>
{{-- SECTION DOKUMENTASI --}}
<fieldset class="p-3 mt-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title">{{ __('Dokumentasi') }}</legend>
    <div class="grid grid-cols-1 gap-6 mb-4 md:grid-cols-2">

        {{-- Bukti Visual --}}
        <div class="space-y-2">
            <x-form.upload label="Lampirkan Bukti Visual" model="visual_evidence" title="Pilih Gambar" required
                keterangan="Bisa pilih > 1 foto (JPG, PNG)" :file="$visual_evidence" multiple />
            {{-- TAMPILKAN PESAN ERROR DI SINI --}}

            <div wire:loading.remove wire:target="visual_evidence" class="grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-5">
                @if($visual_evidence)
                @foreach($visual_evidence as $index => $image)
                <div class="relative aspect-square group">
                    @php
                    $extension = strtolower($image->getClientOriginalExtension());
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp

                    @if($isImage)
                    <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full border rounded-lg shadow-sm" />
                    @else
                    <div class="flex flex-col items-center justify-center w-full h-full border rounded-lg bg-base-200">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" />
                        </svg>
                        <span class="text-[8px] px-1 truncate w-full text-center">{{ $image->getClientOriginalName() }}</span>
                    </div>
                    @endif

                    <button type="button" wire:click="removeFile('visual_evidence', {{ $index }})"
                        class="absolute flex items-center justify-center w-6 h-6 font-bold text-white rounded-full shadow-md -top-2 -right-2 bg-error">✕</button>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- Dokumen Pendukung --}}
        <div class="space-y-2">
            <x-form.upload label="Lampirkan Dokumen Pendukung" model="supporting_documents" title="Pilih Dokumen" required
                keterangan="PDF atau Word" :file="$supporting_documents" multiple />

            <div wire:loading.remove wire:target="supporting_documents" class="space-y-2">
                @if($supporting_documents)
                @foreach($supporting_documents as $index => $doc)
                @php $docExt = strtolower($doc->getClientOriginalExtension()); @endphp
                <div class="flex items-center justify-between p-2 border border-dashed rounded-lg bg-base-50 border-base-300">
                    <div class="flex items-center gap-2 overflow-hidden">
                        @if($docExt == 'pdf') <x-icon.pdf class="flex-shrink-0 w-5 h-5" />
                        @elseif(in_array($docExt, ['doc', 'docx'])) <x-icon.word class="flex-shrink-0 w-5 h-5" />
                        @else <x-icon.document class="flex-shrink-0 w-5 h-5" /> @endif
                        <span class="text-xs font-medium truncate">{{ $doc->getClientOriginalName() }}</span>
                    </div>
                    <button type="button" wire:click="removeFile('supporting_documents', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</fieldset>

{{-- SECTION TINDAKAN PERBAIKAN --}}
<fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title">{{ __('Tindakan Perbaikan') }}</legend>

    <div class="flex items-center justify-between pb-2 mb-4 border-b">
        <h3 class="text-xs font-bold uppercase md:text-sm text-primary">{{ __('Rencana Perbaikan Jangka Panjang') }}</h3>
        <button type="button" wire:click="addCorrectiveRow" class="btn btn-primary btn-xs sm:btn-sm">
            + {{ __('Tambah') }}
        </button>
    </div>

    {{-- VIEW MOBILE: Tampil di HP (Card Mode) --}}
    {{-- SECTION DOKUMENTASI (EXISTING & TEMPORARY) --}}
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">

        {{-- 1. VISUAL EVIDENCE --}}
        <div class="p-4 border rounded-xl bg-base-100 shadow-sm border-base-300">
            <x-form.upload label="Visual Evidence" model="visual_evidence" multiple keterangan="JPG, PNG (Max 2MB)" />


            <div class="grid grid-cols-3 gap-2 mt-3">
                {{-- DATA DARI DATABASE (EXISTING) --}}
                @foreach($existing_visual_evidence as $media)
                <div class="relative aspect-square group">
                    <img src="{{ asset('storage/' . $media->file_path) }}" class="object-cover w-full h-full border rounded-lg opacity-70" />
                    <div class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-lg">
                        <span class="text-[8px] font-bold text-white bg-success px-1 rounded">SAVED</span>
                    </div>
                    <button type="button" wire:click="deleteMedia({{ $media->file_path }})" wire:confirm="Hapus foto permanen?"
                        class="absolute -top-1 -right-1 btn btn-circle btn-error btn-xs scale-75">✕</button>
                </div>
                @endforeach

                {{-- DATA TEMPORARY (NEW UPLOAD) --}}
                @if($visual_evidence)
                @foreach($visual_evidence as $index => $image)
                <div class="relative aspect-square">
                    <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full border-2 border-primary rounded-lg shadow-md" />
                    <button type="button" wire:click="removeFile('visual_evidence', {{ $index }})"
                        class="absolute -top-1 -right-1 btn btn-circle btn-primary btn-xs scale-75">✕</button>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- 2. SUPPORTING DOCUMENTS --}}
        <div class="p-4 border rounded-xl bg-base-100 shadow-sm border-base-300">
            {{-- Input Upload --}}
            <x-form.upload label="Supporting Docs" model="supporting_documents" multiple keterangan="PDF, DOCX" />

            <div class="mt-3 space-y-2">
                {{-- 1. DATA DARI DATABASE (EXISTING) --}}
                @foreach($existing_supporting_documents as $doc)
                <div class="flex items-center justify-between p-2 border rounded-lg bg-blue-50 border-blue-100 group">
                    <div class="flex items-center gap-2 overflow-hidden">
                        {{-- Deteksi Icon berdasarkan Nama File --}}
                        @php
                        $ext = pathinfo($doc->file_name, PATHINFO_EXTENSION);
                        @endphp

                        @if($ext === 'pdf')
                        <x-icon.pdf class="w-4 h-4 text-red-500 flex-shrink-0" />
                        @elseif(in_array($ext, ['doc', 'docx']))
                        <x-icon.word class="w-4 h-4 text-blue-600 flex-shrink-0" />
                        @else
                        <x-icon.document class="w-4 h-4 text-blue-500 flex-shrink-0" />
                        @endif

                        <div class="flex flex-col min-w-0">
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                class="text-[10px] font-bold text-blue-700 hover:underline truncate">
                                {{ $doc->file_name }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <span class="badge badge-success badge-xs text-[8px] font-bold">SAVED</span>

                        {{-- Tombol Hapus Permanen dari DB --}}
                        <button type="button"
                            wire:click="deleteFileFromDb({{ $doc->id }})"
                            wire:confirm="Hapus dokumen '{{ $doc->file_name }}' secara permanen dari server?"
                            class="btn btn-ghost btn-xs text-error p-0 h-5 w-5 min-h-0">
                            ✕
                        </button>
                    </div>
                </div>
                @endforeach

                {{-- 2. DATA TEMPORARY (NEW UPLOAD) --}}
                @if($supporting_documents)
                @foreach($supporting_documents as $index => $doc)
                <div class="flex items-center justify-between p-2 border border-dashed rounded-lg bg-base-200 border-base-300">
                    <div class="flex items-center gap-2 overflow-hidden opacity-75">
                        <x-icon.document class="w-4 h-4 text-gray-500 flex-shrink-0" />
                        <span class="text-[10px] truncate italic">{{ $doc->getClientOriginalName() }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <span class="text-[8px] font-medium text-gray-500 uppercase">Pending</span>
                        <button type="button"
                            wire:click="removeFile('supporting_documents', {{ $index }})"
                            class="btn btn-ghost btn-xs text-error p-0 h-5 w-5 min-h-0">
                            ✕
                        </button>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- VIEW DESKTOP: Tampil di Tablet/Laptop (Table Mode) --}}
    <div class="hidden overflow-x-auto md:block">
        <table class="table w-full table-compact">
            <thead>
                <tr class="text-[11px] uppercase bg-base-200">
                    <th class="rounded-l-lg">Rencana Perbaikan</th>
                    <th>Hirarki</th>
                    <th>PIC</th>
                    <th>Batas Waktu</th>
                    <th>Tgl. Selesai</th>
                    <th class="rounded-r-lg"></th>
                </tr>
            </thead>
            <tbody class="text-xs">
                @foreach($corrective_actions as $index => $action)
                <tr wire:key="corrective-desktop-{{ $index }}-{{ count($corrective_actions) }}" class="hover:bg-base-50">
                    <td class="w-1/4 align-top">
                        <x-form.text_area model="corrective_actions.{{ $index }}.action_description" rows="2" />
                    </td>
                    <td class="w-1/5 align-top">
                        <x-form.select model="corrective_actions.{{ $index }}.control_hierarchy"
                            :options="[['id'=>'Eliminasi','name'=>'Eliminasi'],['id'=>'Substitusi','name'=>'Substitusi'],['id'=>'Engineering','name'=>'Rekayasa'],['id'=>'Administrasi','name'=>'Admin'],['id'=>'APD','name'=>'APD']]" />
                    </td>
                    <td class="w-1/5 align-top">
                        <x-form.searchable-select-advanced modelsearch="searchPetugas.{{ $index }}"
                            modelid="corrective_actions.{{ $index }}.pic_user_id" :options="$pelaporsAct"
                            :showdropdown="$showDropdownPetugas[$index] ?? false" clickaction="selectActPelapor" />
                    </td>
                    <td class="align-top">
                        <x-form.tgl-waktu model="corrective_actions.{{ $index }}.due_date" />
                    </td>
                    <td class="align-top">
                        <x-form.tgl-waktu model="corrective_actions.{{ $index }}.actual_completion_date" />
                        {{-- Desktop Indicators (Icon-only to save space) --}}
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
                    <td class="align-top">
                        @if(count($corrective_actions) > 1)
                        <button type="button" wire:click="removeCorrectiveRow({{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</fieldset>
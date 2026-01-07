@props([
    'label' => 'Lampirkan foto atau dokumentasi',
    'id' => 'upload-' . uniqid(),
    'model' => null,           // wire:model untuk file baru (e.g., new_doc_deskripsi)
    'existingFile' => null,    // Variabel untuk path file lama (e.g., $doc_deskripsi)
    'newFile' => null,         // Variabel untuk object TemporaryUploadedFile (e.g., $new_doc_deskripsi)
    'isDisabled' => false,
    'optional' => true,
])

@php
    // Logika penentuan file yang akan dipreview
    $fileToPreview = $newFile ?? ($existingFile ? (object) ['name' => $existingFile] : null);

    $fileName = null;
    $extension = null;
    $isImage = false;
    $fileUrl = null;

    if ($fileToPreview) {
        $fileName = ($newFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
            ? $newFile->getClientOriginalName()
            : (is_object($fileToPreview) ? $fileToPreview->name : $existingFile);

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        $fileUrl = $newFile
            ? (method_exists($newFile, 'temporaryUrl') ? $newFile->temporaryUrl() : null)
            : ($existingFile ? asset('storage/' . $existingFile) : null);
    }
@endphp

<fieldset {{ $attributes->merge(['class' => 'fieldset']) }}>
    <x-form.label :label="$label . ($optional ? ' (optional)' : '')" />

    {{-- Trigger Area --}}
    <label wire:ignore for="{{ $id }}"
        class="flex items-center gap-2 {{ $isDisabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }} border border-info rounded hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden transition-all">

        <span class="btn btn-info btn-xs {{ $isDisabled ? 'btn-disabled' : '' }}">
            Pilih file atau gambar
        </span>

        <span id="name-{{ $id }}" class="text-[9px] text-gray-500 truncate max-w-sm">
            @if($newFile)
                {{ $newFile->getClientOriginalName() }}
            @elseif($existingFile)
                {{ $existingFile }}
            @else
                Belum ada file
            @endif
        </span>
    </label>

    {{-- Preview Area --}}
    <div class="mt-2">
        @if ($fileToPreview)
            <div class="text-[10px] font-medium {{ $newFile ? 'text-green-600' : 'text-gray-600' }}">
                {{ $newFile ? 'Preview file baru:' : 'File lama:' }}
            </div>

            @if ($isImage && $fileUrl)
                <div class="relative inline-block">
                    <img src="{{ $fileUrl }}" class="object-cover h-24 mt-1 border rounded shadow-sm">
                    <a href="{{ $fileUrl }}" target="_blank" class="block text-[10px] text-blue-500 hover:underline mt-1">Lihat Fullscreen</a>
                </div>
            @else
                <div class="flex items-center gap-2 mt-1">
                    @if ($extension == 'pdf')
                        <x-icon.pdf class="w-8 h-8" />
                    @elseif (in_array($extension, ['doc', 'docx']))
                        <x-icon.word class="w-8 h-8" />
                    @else
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                        </svg>
                    @endif

                    <div class="flex flex-col">
                        <span class="text-xs font-semibold truncate max-w-[200px] {{ $extension == 'pdf' ? 'text-red-600' : ($extension == 'doc' || $extension == 'docx' ? 'text-blue-600' : 'text-gray-600') }}">
                            {{ $fileName }}
                        </span>
                        @if($fileUrl && !$newFile)
                             <a href="{{ $fileUrl }}" target="_blank" class="text-[10px] text-blue-500 hover:underline">Download / Lihat</a>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <span class="text-xs text-gray-400">Belum ada file</span>
        @endif
    </div>

    {{-- Hidden Input --}}
    <input
        type="file"
        id="{{ $id }}"
        {{ $isDisabled ? 'disabled' : '' }}
        wire:model.live="{{ $model }}"
        class="hidden"
        onchange="document.getElementById('name-{{ $id }}').textContent = this.files[0]?.name ?? 'Belum ada file'"
    />

    @if($model)
        <x-label-error :messages="$errors->get($model)" />
    @endif
</fieldset>

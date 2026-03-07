<fieldset class="p-3 my-4 mt-12 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title ">{{ __('Dokumentasi') }}</legend>
    <div class="grid grid-cols-1 gap-4 mb-8 space-y-4 md:grid-cols-2 ">
        <fieldset class="fieldset">
            <x-form.upload label="Lampirkan Bukti Visual" model="visual_evidence" title="Pilih Gambar" keterangan="Pilih Bukti Visual: Foto"
                :file="$visual_evidence" />

            <div wire:loading.remove wire:target="visual_evidence">
                @if ($visual_evidence)
                @if (in_array($visual_evidence->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                {{-- Preview Gambar --}}
                <img src="{{ $visual_evidence->temporaryUrl() }}"
                    class="w-40 h-auto mt-2 border rounded" />
                @else
                {{-- Fallback jika format lain --}}
                <p class="mt-2 text-sm text-gray-600">
                    File: {{ $visual_evidence->getClientOriginalName() }}
                </p>
                @endif
                @endif
            </div>

            <x-label-error :messages="$errors->get('visual_evidence')" />
        </fieldset>
        <fieldset class="fieldset">
            <x-form.upload label="Lampirkan Dokumen Pendukung" model="supporting_documents" title="Pilih Dokumen" keterangan="Pilih Dokumen Pendukung: Word, PDF" :file="$supporting_documents" />

            <div wire:loading.remove wire:target="supporting_documents">
                @if ($supporting_documents)
                {{-- CEK DOKUMEN --}}
                @if (in_array($supporting_documents->getClientOriginalExtension(), ['pdf', 'doc', 'docx']))
                <div class="flex items-center gap-2 mt-2">
                    @if ($supporting_documents->getClientOriginalExtension() == 'pdf')
                    <x-icon.pdf class="w-8 h-8" />
                    <span class="text-sm text-red-600">{{ $supporting_documents->getClientOriginalName() }}</span>
                    @elseif (in_array($supporting_documents->getClientOriginalExtension(), ['doc', 'docx']))
                    <x-icon.word class="w-8 h-8" />
                    <span class="text-sm text-blue-600">{{ $supporting_documents->getClientOriginalName() }}</span>
                    @else
                    {{-- Ikon generik --}}
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                    </svg>
                    <span class="text-sm text-gray-600">{{ $supporting_documents->getClientOriginalName() }}</span>
                    @endif
                </div> {{-- DIV PENUTUP DARI FLEX ITEM --}}

                {{-- FALLBACK JIKA BUKAN GAMBAR MAUPUN DOKUMEN DI ATAS --}}
                @else
                <p class="mt-2 text-sm text-gray-600">
                    File: {{ $supporting_documents->getClientOriginalName() }}
                </p>
                @endif {{-- TUTUP if UTAMA --}}
                @endif
            </div> {{-- TUTUP wire:loading.remove --}}

            <x-label-error :messages="$errors->get('supporting_documents')" />
        </fieldset>
    </div>
</fieldset>
<fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title ">{{ __('Tindakan Perbaikan') }}</legend>
    <x-form.textarea label="Deskripsi Tindakan" model="action_description" placeholder="Jelaskan tindakan perbaikan yang sudah dilakukan atau direncanakan..." />
    <div class="grid grid-cols-1 gap-4 mt-12 mb-8 space-y-4 md:grid-cols-2 ">
        <x-form.tgl-waktu label="Batas Waktu Penyelesaian" model="completion_deadline" :min-date="now()->format('Y-m-d\TH:i')" />
        <x-form.tgl-waktu label="Tanggal Penyelesaian Tindakan" model="actual_completion_date" :min-date="now()->format('Y-m-d\TH:i')" />
    </div>
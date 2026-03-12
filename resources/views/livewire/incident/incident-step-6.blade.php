<div class="mt-6 overflow-x-auto">
    <table class="table w-full table-xs border border-collapse border-base-300">
        <thead>
            {{-- Sub-Header Penyebab Langsung --}}
            <tr class="text-center bg-orange-100">
                <th colspan="3" class="py-1 italic font-bold border text-base-content border-base-300">
                    PENYEBAB LANGSUNG
                </th>
            </tr>
            {{-- Header Kolom --}}
            <tr class="bg-gray-200 text-base-content">
                <th class="w-1/2 px-4  font-bold border border-base-300">Kondisi Tidak Aman</th>
                <th class="w-1/2 px-4  font-bold border border-base-300">Description</th>
                <th class="w-10 border border-base-300"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($unsafe_conditions as $index => $row)
            <tr wire:key="unsafe-condition-{{ $index }}">
                <td class="p-1 align-top border border-base-300">
                    {{-- Menggunakan komponen select yang Anda berikan --}}
                    <x-form.select
                        model="unsafe_conditions.{{ $index }}.item"
                        :options="$this->unsafeConditionOptions"
                        placeholder="Choose an item." />
                </td>
                <td class="p-1 align-top border border-base-300">
                    <x-form.text_area
                        wire:model="unsafe_conditions.{{ $index }}.description"
                        placeholder="Tambahkan rincian deskripsi..."
                        rows="2" />
                </td>
                <td class="p-1 text-center align-middle border border-base-300">
                    @if(count($unsafe_conditions) > 1)
                    <button type="button" wire:click="removeRow('unsafe_conditions', {{ $index }})"
                        class="btn btn-ghost btn-xs text-error">✕</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tombol Tambah Baris --}}
    <div class="mt-2">
        <button type="button" wire:click="addRow('unsafe_conditions')"
            class="btn btn-sm btn-outline btn-success">
            + Tambah Kondisi Tidak Aman
        </button>
    </div>
</div>
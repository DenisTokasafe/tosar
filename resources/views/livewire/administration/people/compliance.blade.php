<section class="w-full">
    <x-toast />
    <x-tabs-people.layout :idUser="$userId">

        <div class="flex flex-col items-center p-2 border rounded-t-lg md:flex-row md:justify-between border-neutral-200 ">
            <div class="rounded ">
                Logo/Brand
            </div>
            <div class="flex gap-2 rounded">
                <x-button.btn-tooltip color="primary" icon="add" modalId="create_modal" tooltip="Tambah Employee" />
                @livewire('administration.people.compliance-import')
            </div>
        </div>

        <div class="overflow-x-auto border">
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Compliance Name</th>
                        <th>Start Date</th>
                        <th>Expiry Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compliances as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        {{-- Mengambil nama dari relasi master --}}
                        <td>{{ $item->master->title ?? 'N/A' }}</td>
                        <td>{{ $item->start_date }}</td>
                        <td>
                            {{-- Logika untuk menampilkan NULL sebagai Lifetime --}}
                            <span class="badge {{ $item->expired_at ? 'badge-ghost' : 'badge-success' }}">
                                {{ $item->expired_at ?: 'Lifetime/Permanen' }}
                            </span>
                        </td>
                        <td class="gap-2">
                            <x-button.btn-tooltip color="warning" icon="edit" tooltip="Details" />
                            <x-button.btn-tooltip color="error" icon="delete" modalId="delete_modal" tooltip="Hapus" />

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <dialog wire:ignore.self id="create_modal" class="modal">
            <div class="modal-box">

                <fieldset class="fieldset">
                        <x-form.label label="Pilih Class" required />
                        <select wire:model.live="compliance_class"
                            class="w-full select select-bordered select-xs focus-within:outline-none focus-within:border-info focus-within:ring-0">
                            <option value="">-- Select Existing Class --</option>
                            {{-- Loop data class yang unik dari database --}}
                            @foreach($this->existing_classes as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('class')" />
                    </fieldset>
                <fieldset class="fieldset">
                        <x-form.label label="Description" required />
                        <select wire:model.live="compliance_name"
                            class="w-full select select-bordered select-xs focus-within:outline-none focus-within:border-info focus-within:ring-0">
                            <option value="">-- Select Existing Compliance --</option>
                            {{-- Loop data class yang unik dari database --}}
                            @foreach($this->existing_name as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('class')" />
                    </fieldset>
                <div class="modal-action">
                    <form method="dialog">
                        <!-- if there is a button in form, it will close the modal -->
                        <button class="btn btn-xs btn-error btn-soft">Close</button>
                    </form>
                </div>
            </div>
        </dialog>
    </x-tabs-people.layout>
</section>

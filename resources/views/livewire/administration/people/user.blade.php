<section class="w-full">
    <x-toast />
    <x-tabs-relation.layout>
        <div class="p-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex flex-row">
                    <flux:tooltip content="Bulk Update" position="top">
                        <flux:button size="xs" variant="primary" icon='refresh-cw'
                            wire:click="$set('showBulkUpdateModal', true)"></flux:button>
                    </flux:tooltip>
                    <div wire:ignore class="mx-2 w-60">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                           autocomplete="off" placeholder="Cari Pelapor..." readonly onfocus="this.removeAttribute('readonly');"
                            class="input input-xs focus-within:outline-none focus-within:border-info focus-within:ring-0" />
                    </div>
                </div>
                <div>

                    <flux:tooltip content="tambah data" position="top">
                        <flux:button size="xs" wire:click="create" icon="add-icon" variant="primary"></flux:button>
                    </flux:tooltip>
                    <flux:tooltip content="Import data" position="top">
                        <flux:button size="xs" wire:click="$set('showImportModal', true)" icon="import"
                            variant="subtle"></flux:button>
                    </flux:tooltip>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" wire:model.live="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Username</th>
                            <th>Department</th>
                            <th>Employee ID</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" value="{{ $user->id }}" wire:model.live="selectedUsers">
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->gender }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->department_name }}</td>
                                <td>{{ $user->employee_id }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="flex gap-2">
                                    <!-- Edit -->

                                    <div class="tooltip tooltip-right ">
                                        <div class="tooltip-content ">
                                            <div class="text-xs font-black animate-bounce text-warning">Edit</div>
                                        </div>
                                        <flux:button wire:click="edit({{ $user->id }})" size="xs"
                                            icon="pencil-square" variant="subtle"></flux:button>
                                    </div>
                                    <!-- Delete -->
                                    <div class="tooltip tooltip-right ">
                                        <div class="tooltip-content ">
                                            <div class="text-xs font-black animate-bounce text-error">Delete</div>
                                        </div>
                                        <flux:button wire:click="confirmDelete({{ $user->id }})" size="xs"
                                            icon="trash" variant="danger"></flux:button>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    </x-tabs-relation.layout>
    {{ $users->links() }}

    {{-- Create/Edit Modal --}}
    <dialog class="modal" @if ($showModal) open @endif>
        <div class="w-11/12 max-w-2xl modal-box">
            <h3 class="text-lg font-bold">{{ $userId ? 'Edit User ' . $name_user : 'Add User' }}</h3>

            <div class="grid grid-cols-2 gap-4 mt-4">

                <fieldset class="fieldset">
                    <x-form.label label="Nama" required />
                    <input type="text" wire:model.live="name"
                        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('name') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                    <x-label-error :messages="$errors->get('name')" />
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Jenis Kelamin" required />
                    <select wire:model.live="gender"
                        class="w-full select select-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs">
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki - laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <x-label-error :messages="$errors->get('gender')" />
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Tanggal Lahir" required />
                    <input type="text" readonly id="date_birth" wire:model="date_birth"
                        class="w-full cursor-pointer input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs"
                        placeholder="Pilih tanggal lahir {{ $errors->has('date_birth') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
                        x-data="{ fp: null }" {{-- Tambahkan state untuk Flatpickr instance --}} x-init="// Inisialisasi Flatpickr dan simpan instance-nya
                        fp = flatpickr($refs.input, {
                            dateFormat: 'Y-m-d',
                        });

                        // Dengarkan event 'dateLoaded' dari Livewire
                        $wire.on('dateLoaded', () => {
                            // Set tanggal menggunakan nilai Livewire saat event dipanggil
                            if ($wire.date_birth) {
                                fp.setDate($wire.date_birth);
                            }
                        });" x-ref="input" />
                    <x-label-error :messages="$errors->get('date_birth')" />
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Username" :required="!$userId"  />
                    <input type="text" wire:model.live="username"
                        class="w-full input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    <x-label-error :messages="$errors->get('username')" />
                </fieldset>

                <fieldset>
                    <input id="department" value="department" wire:model="deptCont"
                        class="peer/department radio radio-xs radio-accent" type="radio" name="deptCont" checked />
                    <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]"
                        label="PT. MSM & PT. TTN" required />
                    <input id="contractor" value="contractor" wire:model="deptCont"
                        class="peer/contractor radio radio-xs radio-primary" type="radio" name="deptCont" />
                    <x-form.label for="contractor" class="peer-checked/contractor:text-primary" label="Kontraktor"
                        required />

                    <div class="hidden peer-checked/department:block mt-0.5">
                        {{-- Department --}}
                        <div class="relative mb-1">
                            <!-- Input Search -->
                            <input name="search" type="text" wire:model.live.debounce.300ms="search"
                                wire:key="search-dept-{{ $userId }}" placeholder="Cari departemen..."
                                class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('department_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                            <!-- Dropdown hasil search -->
                            @if ($showDropdown && count($departments) > 0)
                                <ul
                                    class="absolute z-10 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">
                                    <!-- Spinner ketika klik salah satu -->
                                    <div wire:loading wire:target="selectDepartment" class="p-2 text-center">
                                        <span class="loading loading-spinner loading-sm text-secondary"></span>
                                    </div>
                                    @foreach ($departments as $dept)
                                        <li wire:click="selectDepartment({{ $dept->id }}, '{{ $dept->department_name }}')"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                            {{ $dept->department_name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        @if ($deptCont === 'department')
                            <x-label-error :messages="$errors->get('department_id')" />
                        @endif
                    </div>
                    <div class="hidden peer-checked/contractor:block mt-0.5">
                        {{-- Contractor --}}
                        <div class="relative mb-1">
                            <!-- Input Search -->
                            <input name="searchContractor" type="text"
                                wire:model.live.debounce.300ms="searchContractor"
                                wire:key="search-contractor-{{ $userId }} placeholder="Cari kontraktor..."
                                class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('contractor_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                            <!-- Dropdown hasil search -->
                            @if ($showContractorDropdown && count($contractors) > 0)
                                <ul
                                    class="absolute z-10 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">
                                    <!-- Spinner ketika klik -->
                                    <div wire:loading wire:target="selectContractor" class="p-2 text-center">
                                        <span class="loading loading-spinner loading-sm text-secondary"></span>
                                    </div>
                                    @foreach ($contractors as $contractor)
                                        <li wire:click="selectContractor({{ $contractor->id }}, '{{ $contractor->contractor_name }}')"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                            {{ $contractor->contractor_name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        @if ($deptCont === 'contractor')
                            <x-label-error :messages="$errors->get('contractor_id')" />
                        @endif
                    </div>
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Employee ID" required />
                    <input type="text" wire:model.live="employee_id"
                        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('employee_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                    <x-label-error :messages="$errors->get('employee_id')" />
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Tanggal masuk" required />
                    <input type="text" readonly id="date_commenced" wire:model="date_commenced"
                        class="cursor-pointer input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('date_commenced') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
                        placeholder="Pilih tanggal masuk" x-data="{ fp: null }" {{-- Tambahkan state untuk Flatpickr instance --}}
                        x-init="// Inisialisasi Flatpickr dan simpan instance-nya
                        fp = flatpickr($refs.input, {
                            dateFormat: 'Y-m-d',
                        });

                        // Dengarkan event 'dateLoaded' dari Livewire
                        $wire.on('dateLoaded', () => {
                            // Set tanggal menggunakan nilai Livewire saat event dipanggil
                            if ($wire.date_commenced) {
                                fp.setDate($wire.date_commenced);
                            }
                        });" x-ref="input" />
                    <x-label-error :messages="$errors->get('date_commenced')" />
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Email" :required="!$userId" />
                    <input type="email" wire:model.live="email"
                        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('email') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                    <x-label-error :messages="$errors->get('email')" />
                </fieldset>
                <fieldset class="fieldset">
                    <x-form.label label="Pilih Peran" required />
                    <select wire:model.live="role_id"
                        class="select select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs{{ $errors->has('role_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                        <option value="">-- Pilih --</option>
                        @foreach ($role as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('role_id')" />
                </fieldset>
                <fieldset class="fieldset">
                    <x-form.label label="Password" required="{{ $userId ? false : true }}" />
                    <input type="password" wire:model="password"
                        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('password') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                    {{-- Teks bantuan saat edit --}}
                    @if ($userId)
                        <p class="text-[8px] text-gray-500 mt-0.5">Kosongkan jika tidak ingin mengubah password.
                        </p>
                    @endif
                    <x-label-error :messages="$errors->get('password')" />
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Konfirmasi Password" required="{{ $userId ? false : true }}" />
                    <input type="password" wire:model="password_confirmation"
                        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('password_confirmation') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                    <x-label-error :messages="$errors->get('password_confirmation')" />
                </fieldset>
            </div>

            <div class="modal-action">
                <flux:button wire:click="save" size="xs" icon:trailing="save" variant="primary">
                    {{ $userId ? 'Update' : 'Simpan' }}</flux:button>
                <flux:button size="xs" wire:click="$set('showModal', false)" icon:trailing="circle-x"
                    variant="danger">Batal</flux:button>
            </div>
        </div>
    </dialog>

    {{-- Delete Confirmation Modal --}}
    <dialog class="modal" @if ($showDeleteModal) open @endif>
        <div class="modal-box">
            <h3 class="text-lg font-bold">Confirm Delete</h3>
            <p>Are you sure you want to delete this user?</p>
            <div class="modal-action">
                <button class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button class="btn btn-error" wire:click="delete">Delete</button>
            </div>
        </div>
    </dialog>
    </div>

    <dialog class="modal" @if ($showImportModal) open @endif>
        <div class="w-11/12 max-w-md modal-box">
            <h3 class="text-lg font-bold">Import Users</h3>

            <fieldset class="fieldset">
                <label class="block">File Excel</label>
                <input type="file" wire:model.live="file"
                    class="w-full input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />

                {{-- Error message --}}
                <x-label-error :messages="$errors->get('file')" />

                {{-- Loading indicator saat pilih file --}}
                <div wire:loading wire:target="file" wire:loading.class.remove="hidden"
                    class="hidden mt-1 text-sm text-info">
                    ⏳ Sedang mengunggah file...
                </div>
            </fieldset>
            @if (session()->has('success'))
                <div class="my-2 alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="modal-action">
                {{-- Tombol Import --}}
                <flux:button wire:click="import" size="xs" icon:trailing="save" variant="primary"
                    wire:loading.attr="disabled" wire:target="import,file">

                    <span wire:loading.remove wire:target="import,file">Import</span>
                    <span wire:loading.class.remove='hidden' class="hidden"
                        wire:target="import,file">Mengimpor...</span>
                </flux:button>

                {{-- Tombol Batal --}}
                <flux:button size="xs" wire:click="$set('showImportModal', false)" wire:loading.attr="disabled"
                    wire:target="import,file" {{-- ⬅️ PERBAIKAN DI SINI --}} icon:trailing="circle-x" variant="danger">
                    Batal
                </flux:button>
            </div>
        </div>
    </dialog>

    <dialog class="modal" @if ($showBulkUpdateModal) open @endif>
        <div class="modal-box">
            <h3 class="text-lg font-bold">Bulk Update User</h3>

            <fieldset class="fieldset">
                <label class="block">Role Baru</label>
                <select wire:model="bulkRole" class="w-full select select-bordered input-xs">
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </fieldset>
            <div class="modal-action">
                <flux:button size="xs" wire:click="bulkUpdate" variant="primary">Update</flux:button>
                <flux:button size="xs" wire:click="$set('showBulkUpdateModal', false)" variant="danger">Batal
                </flux:button>
            </div>
        </div>
    </dialog>
</section>

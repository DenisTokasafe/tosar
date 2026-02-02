<div class="flex flex-col w-full gap-6 ">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
        <span class="flex items-center justify-center mb-1 rounded-md h-9 w-9">
            <x-app-logo-icon class="text-black fill-current size-9 dark:text-white" />
        </span>
        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
    </a>
    <x-auth-header :title="__('Pendaftaran Akun')" :description="__('Isi data di bawah untuk mendaftar.')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />
    <!-- Validation Errors -->
    <div class="w-full join">
        <div>
            <label
                class="w-full input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs validator join-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-id-card-icon lucide-id-card">
                    <path d="M16 10h2" />
                    <path d="M16 14h2" />
                    <path d="M6.17 15a3 3 0 0 1 5.66 0" />
                    <circle cx="9" cy="11" r="2" />
                    <rect x="2" y="5" width="20" height="14" rx="2" />
                </svg>
                <input wire:model.live="check_id" type="text" placeholder="input nomor id anda" required />
            </label>
            <div class="hidden validator-hint">Enter valid email address</div>
        </div>
        <button wire:click='checkId' class="btn btn-xs btn-neutral join-item">Check ID</button>
    </div>
    @if ($check_no_id_status)
        <p class="text-xs {{ str_contains($check_no_id_status, 'tersedia') ? 'text-green-500' : 'text-red-500' }}">
            {{ $check_no_id_status }}
        </p>
    @endif

    <form wire:submit="register">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <!-- Name -->
            <flux:input wire:model.live="first_name" :label="__('Nama')" type="text" required autofocus
                autocomplete="Nama Lengkap" :placeholder="__('Nama ')" />
            <!-- Name -->
            <flux:input wire:model.live="last_name" :label="__('Marga')" type="text" required autofocus
                autocomplete="last_name" :placeholder="__('Marga')" />
            <!-- Name -->
            <flux:input wire:model.live="name" :label="__('Nama yang di tampilkan')" type="text" disabled required
                autofocus autocomplete="name" :placeholder="__('Nama yang di tampilkan')" />

            <!-- Username -->
            <flux:input wire:model="username" :label="__('Username')" type="text" required autocomplete="username"
                :placeholder="__('Username')" />

            <!-- Email Address -->
            <flux:input wire:model="email" :label="__('Alamat Email')" type="email" required autocomplete="email"
                placeholder="email@example.com" />
            <!-- Nomor ID -->
            <flux:input wire:model="no_id" :label="__('Nomor ID')" type="text" required autocomplete="no_id"
                :placeholder="__('Nomor ID')" />
            <!-- Jenis kelamin -->
            <flux:select wire:model="jenis_kelamin" placeholder="Pilih Jenis Kelamin" :label="__('Jenis Kelamin')"
                required>
                <flux:select.option>Laki-Laki</flux:select.option>
                <flux:select.option>Perempuan</flux:select.option>
            </flux:select>
            <fieldset>
                <input id="department" value="department" wire:model.live="status"
                    class="peer/department radio radio-xs radio-accent" type="radio" name="status" checked />
                <label for="department" class="peer-checked/department:text-accent">Departemen @if ($status === 'department')
                        <span class="text-xs font-bold text-red-500">*</span>
                    @endif
                </label>
                <input id="company" value="company" wire:model.live="status"
                    class="peer/company radio radio-xs radio-primary" type="radio" name="status" />
                <label for="company" class="peer-checked/company:text-primary">Kontraktor @if ($status === 'company')
                        <span class="text-xs font-bold text-red-500">*</span>
                    @endif
                </label>

                <div class="hidden peer-checked/department:block mt-0.5">
                    {{-- Department --}}
                    <div class="relative mb-1">
                        <!-- Input Search -->
                        <flux:input wire:model.live.debounce.300ms="searchDepartemen" type="text" autofocus
                            :placeholder="__('Department')" />
                        <!-- Dropdown hasil search -->
                        @if ($showDepartemenDropdown && count($departments) > 0)
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
                    @if ($status === 'department')
                        <x-label-error :messages="$errors->get('searchDepartemen')" />
                    @endif
                </div>
                <div class="hidden peer-checked/company:block mt-0.5">
                    {{-- Contractor --}}
                    <div class="relative mb-1">
                        <!-- Input Search -->
                        <flux:input wire:model.live.debounce.300ms="searchContractor" type="text" autofocus
                            :placeholder="__('Kontraktor')" />
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
                    @if ($status === 'company')
                        <x-label-error :messages="$errors->get('searchContractor')" />
                    @endif
                </div>
            </fieldset>

            <!-- Password -->
            <flux:input wire:model="password" :label="__('Password')" type="password" required
                autocomplete="new-password" :placeholder="__('Password')" viewable />

            <!-- Confirm Password -->
            <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm password')" viewable />
        </div>
        <div class="flex items-center justify-end mt-4">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>

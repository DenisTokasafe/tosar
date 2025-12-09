<div class="flex flex-1 gap-6 bg-base-300">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Name -->
        <flux:input wire:model="name" :label="__('Nama')" type="text" required autofocus autocomplete="name" :placeholder="__('Nama Lengkap')" />

        <!-- Username -->
        <flux:input wire:model="username" :label="__('Username')" type="text" required autocomplete="username" :placeholder="__('Username')" />

        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Alamat Email')" type="email" required autocomplete="email" placeholder="email@example.com" />

        <fieldset>
                <input id="department" value="department" wire:model.live="status" class="peer/department radio radio-xs radio-accent" type="radio" name="status" checked />
                <label for="department" class="peer-checked/department:text-accent">Departemen @if($status ==="department") <span class="text-red-500 font-bold text-xs">*</span> @endif</label>

                <input id="company" value="company" wire:model.live="status" class="peer/company radio radio-xs radio-primary" type="radio" name="status" />
                <label for="company" class="peer-checked/company:text-primary">Kontraktor @if($status ==="company") <span class="text-red-500 font-bold text-xs">*</span> @endif</label>

                <div class="hidden peer-checked/department:block mt-0.5">
                    {{-- Department --}}
                    <div class="relative mb-1">
                        <!-- Input Search -->
                        <flux:input wire:model.live.debounce.300ms="searchDepartemen"  type="text" required autofocus  :placeholder="__('Department')" />
                        <!-- Dropdown hasil search -->
                        @if($showDepartemenDropdown && count($departments) > 0)
                        <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                            <!-- Spinner ketika klik salah satu -->
                            <div wire:loading wire:target="selectDepartment" class="p-2 text-center">
                                <span class="loading loading-spinner loading-sm text-secondary"></span>
                            </div>
                            @foreach($departments as $dept)
                            <li wire:click="selectDepartment({{ $dept->id }}, '{{ $dept->department_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                {{ $dept->department_name }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @if($status === 'department')
                    <x-label-error :messages="$errors->get('department_id')" />
                    @endif
                </div>
                <div class="hidden peer-checked/company:block mt-0.5">
                    {{-- Contractor --}}
                    <div class="relative mb-1">
                        <!-- Input Search -->
                        <flux:input wire:model.live.debounce.300ms="searchContractor"  type="text" required autofocus  :placeholder="__('Kontraktor')" />
                        <!-- Dropdown hasil search -->
                        @if($showContractorDropdown && count($contractors) > 0)
                        <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                            <!-- Spinner ketika klik -->
                            <div wire:loading wire:target="selectContractor" class="p-2 text-center">
                                <span class="loading loading-spinner loading-sm text-secondary"></span>
                            </div>
                            @foreach($contractors as $contractor)
                            <li wire:click="selectContractor({{ $contractor->id }}, '{{ $contractor->contractor_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                {{ $contractor->contractor_name }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @if($status === 'company')
                    <x-label-error :messages="$errors->get('contractor_id')" />
                    @endif
                </div>
            </fieldset>

        <!-- Password -->
        <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password" :placeholder="__('Password')" viewable />

        <!-- Confirm Password -->
        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>

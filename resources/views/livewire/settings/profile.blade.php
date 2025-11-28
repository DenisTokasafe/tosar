<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your data account profile information and email address.')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                <flux:input wire:model.blur="name" :label="__('Name')" type="text" autofocus autocomplete="name" />
                <flux:input wire:model.blur="username" :label="__('username')" type="text" autofocus autocomplete="username" />
                <flux:input wire:model.blur="employee_id" :label="__('employee_id')" type="text" autofocus autocomplete="employee_id" />
                <fieldset>
                    <input id="department" value="department" wire:model="deptCont"
                        class="peer/department radio radio-sm radio-accent" type="radio" name="deptCont" checked />
                    <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]"
                        label="PT. MSM & PT. TTN" required />
                    <input id="contractor" value="contractor" wire:model="deptCont"
                        class="peer/contractor radio radio-sm radio-primary" type="radio" name="deptCont" />
                    <x-form.label for="contractor" class="peer-checked/contractor:text-primary" label="Kontraktor"
                        required />

                    <div class="hidden peer-checked/department:block mt-0.5">
                        {{-- Department --}}
                        <div class="relative mb-1">
                            <!-- Input Search -->
                            <flux:input wire:model.blur="search" type="text" class="w-full" autofocus
                                autocomplete="name" />
                            <!-- Dropdown hasil search -->
                            @if ($showDropdown && count($departments) > 0)
                                <ul
                                    class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
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
                            <x-label-error :messages="$errors->get('department_name')" />
                        @endif
                    </div>
                    <div class="hidden peer-checked/contractor:block mt-0.5">
                        {{-- Contractor --}}
                        <div class="relative mb-1">
                            <!-- Input Search -->
                            <flux:input wire:model.blur="searchContractor" type="text" class="w-full" autofocus
                                autocomplete="name" />
                            <!-- Dropdown hasil search -->
                            @if ($showContractorDropdown && count($contractors) > 0)
                                <ul
                                    class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
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
                            <x-label-error :messages="$errors->get('department_name')" />
                        @endif
                    </div>
                </fieldset>
                <div>
                    <flux:input wire:model="email" :label="__('Email')" type="email" required
                        autocomplete="email" />

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                        <div>
                            <flux:text class="mt-4">
                                {{ __('Your email address is unverified.') }}

                                <flux:link class="text-sm cursor-pointer"
                                    wire:click.prevent="resendVerificationNotification">
                                    {{ __('Click here to re-send the verification email.') }}
                                </flux:link>
                            </flux:text>

                            @if (session('status') === 'verification-link-sent')
                                <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </flux:text>
                            @endif
                        </div>
                    @endif
                </div>
                <flux:input wire:model.blur="date_birth" :label="__('Date of Birth')" type="text" autofocus
                    autocomplete="date_birth" x-data x-init="flatpickr($refs.input, { dateFormat: 'Y-m-d' })" x-ref="input"/>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>

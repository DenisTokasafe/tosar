<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your username or email and password below to log in')" />
    {{-- ^ Deskripsi diubah --}}

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <flux:input
            size="sm"
            wire:model="credential"  {{-- <-- DIUBAH dari wire:model="email" --}}
            :label="__('Username or Email')" {{-- <-- Label diubah --}}
            type="text"                  {{-- <-- type diubah ke text (bukan email) --}}
            required
            autofocus
            autocomplete="username"      {{-- <-- autocomplete diubah --}}
            placeholder="username atau email@example.com"
        />

        <div class="relative ">
            <flux:input size="sm" wire:model="password" :label="__('Password')" type="password" required autocomplete="current-password" :placeholder="__('Password')" viewable />
            @if (Route::has('password.request'))
            <flux:link class="absolute end-0 top-0 text-sm " :href="route('password.request')" wire:navigate>
                {{ __('Forgot your password?') }}
            </flux:link>
            @endif
        </div>

        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>

    {{-- ... (bagian register) ... --}}
</div>

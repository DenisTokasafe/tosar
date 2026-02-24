<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Delete account') }}</flux:heading>
        <flux:subheading>{{ __('Delete your account and all of its resources') }}</flux:subheading>
    </div>


        <button class="btn btn-soft btn-error btn-xs" x-data="" onclick="my_modal_2.showModal()">
            {{ __('Delete account') }}
        </button>


    <dialog id="my_modal_2" class="modal" wire:ignore.self>
        <form wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Are you sure you want to delete your account?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </flux:subheading>
            </div>
             <x-form.input-text label="Password" type='password' model="password" placeholder="password..." required />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <form method="dialog" class="modal-backdrop">
                    <button class="btn btn-soft btn-secondary btn-xs">{{ __('Cancel') }}</button>
                </form>
                <button class="btn btn-soft btn-error btn-xs" type="submit">{{ __('Delete account') }}</button>
            </div>
        </form>
    </dialog>
</section>

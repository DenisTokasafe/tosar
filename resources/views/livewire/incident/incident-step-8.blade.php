<fieldset class="mt-4 fieldset lg:col-span-2">
    <x-form.label label="Deskripsi" required />
    <div x-data="ckeditorHelper('description')" wire:ignore>
        <div x-ref="editorElement" data-placeholder="Masukkan deskripsi..."></div>
    </div>
    <x-label-error :messages="$errors->get('description')" />
</fieldset>
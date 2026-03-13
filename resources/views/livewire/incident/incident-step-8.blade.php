<fieldset class="mt-4 fieldset lg:col-span-2">
    <x-form.label label="Kunci Pembelajaran" required />
    <div x-data="ckeditorHelper('key_learniing')" wire:ignore>
        <div x-ref="editorElement" data-placeholder="Masukkan kunci pembelajaran..."></div>
    </div>
    <x-label-error :messages="$errors->get('key_learniing')" />
</fieldset>
<section class="w-full">
    <x-toast />
    <x-tabs-people.layout :idUser="$userId">
        <div class="flex flex-col items-center p-4 bg-gray-100 md:flex-row md:justify-between">
            <div class="text-white bg-blue-500 rounded ">
                Logo/Brand
            </div>

            <div class="text-white bg-green-500 rounded ">
                <x-button.btn-tooltip color="primary" icon="add" modalId="create_modal" tooltip="Tambah Employee" />
                <x-button.btn-tooltip modalId="import_modal" color="accent" icon="file-import" tooltip="Import Data" />
            </div>
        </div>
    </x-tabs-people.layout>
</section>

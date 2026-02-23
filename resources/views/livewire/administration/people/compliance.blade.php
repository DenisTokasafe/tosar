<section class="w-full">
    <x-toast />
    <x-tabs-people.layout :idUser="$userId">
        <div class="flex flex-col items-center p-2 border rounded-t-lg md:flex-row md:justify-between border-neutral-200 md:absolute md:inset-x-0 md:top-0 md:z-20" >
            <div class="rounded ">
                Logo/Brand
            </div>

            <div class="rounded ">
                <x-button.btn-tooltip color="primary" icon="add" modalId="create_modal" tooltip="Tambah Employee" />
                <x-button.btn-tooltip modalId="import_modal" color="accent" icon="file-import" tooltip="Import Data" />
            </div>
        </div>
        <div class="mt-5 overflow-x-auto">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>Compliance</th>
                            <th>Compliance Date</th>
                            <th>Compliance Expiry Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
    </x-tabs-people.layout>
</section>

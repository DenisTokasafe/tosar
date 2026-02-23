<section class="w-full">
    <x-toast />
    <x-tabs-people.layout :idUser="$userId">
        <div class="flex flex-col items-center p-4 bg-gray-100 md:flex-row md:justify-between">
            <div class="p-4 text-white bg-blue-500 rounded">
                Logo/Brand
            </div>

            <div class="p-4 text-white bg-green-500 rounded">
                Menu Navigasi
            </div>
        </div>
    </x-tabs-people.layout>
</section>

<section class="w-full">
    <x-toast />
    <x-tabs-people.layout :idUser="$userId">
        <div class="flex flex-col items-center p-2 border rounded-t-lg md:flex-row md:justify-between border-neutral-200 ">
            <div class="rounded ">
                Logo/Brand
            </div>

            <div class="flex rounded">
                <x-button.btn-tooltip color="primary" icon="add" modalId="create_modal" tooltip="Tambah Employee" />
                @livewire('administration.people.compliance-import')
            </div>
        </div>
        <div class="mt-5 overflow-x-auto">
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Compliance Name</th>
                        <th>Start Date</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compliances as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        {{-- Mengambil nama dari relasi master --}}
                        <td>{{ $item->master->name ?? 'N/A' }}</td>
                        <td>{{ $item->start_date }}</td>
                        <td>
                            {{-- Logika untuk menampilkan NULL sebagai Lifetime --}}
                            <span class="badge {{ $item->expired_at ? 'badge-ghost' : 'badge-success' }}">
                                {{ $item->expired_at ?: 'Lifetime/Permanen' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-tabs-people.layout>
</section>

<section class="w-full">
    <x-toast />
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    @include('partials.kustodian-heading')
    <div class="flex justify-between ">
        <flux:tooltip content="tambah data" position="top">
            <flux:button size="xs" wire:click='open_modal' icon="add-icon" variant="primary"></flux:button>
        </flux:tooltip>
        <div class='md:flex-row flex-col flex gap-2'>
            <flux:input size='xs' icon="magnifying-glass" wire:model.blur='search_department' placeholder="Search departemen" />
        </div>
    </div>
    <x-manhours.layout>
        <div class="overflow-x-auto ">
            <table class="table table-xs table-zebra">
                <thead class="text-center">
                    <tr>
                        <th># </th>
                        <th colspan="2">{{ __('Kustodian') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($Departments as $no => $dept)
                    <tr class="even:bg-base-200 odd:bg-white hover:bg-accent/25">
                        <th class="text-center">{{ $Departments->firstItem() + $no }}</th>
                        <th>
                            <div class='flex justify-center'>
                                <span class=" w-full max-w-40">{{$dept->department_name }}</span>
                            </div>
                        </th>
                        <th class='flex justify-center'>
                            @if ($dept->contractors()->count()>0)
                            <table class='justify-items-center'>
                                <thead>
                                    <tr class="text-center">
                                        <th>{{ __('Nama Kontraktor') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                @foreach ($dept->contractors()->get() as $contractor )
                                <tr class="text-center">
                                    <td>{{ $contractor->contractor_name }}</td>
                                    <th class='flex justify-center flex-row gap-2'>
                                        <flux:tooltip content="edit" position="top">
                                            <flux:button wire:click="modalEdit({{ $dept->id }}, {{ $contractor->id }})" size="xs" icon="pencil-square" variant="subtle"></flux:button>
                                        </flux:tooltip>
                                        <flux:tooltip content="hapus" position="top">
                                            <flux:button wire:click="confirmDelete" size="xs" icon="trash" variant="danger"></flux:button>
                                        </flux:tooltip>
                                    </th>
                                </tr>
                                <flux:modal name="delete-custodian" class="min-w-[22rem]">
                                    <div class="space-y-6">
                                        <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs sm:w-sm border p-4 sm:justify-self-center">
                                            <legend class="fieldset-legend">
                                                <flux:heading size="md">{{ __('Delete Kustodian') }}?</flux:heading>
                                            </legend>
                                            <flux:text color='accent'>
                                                <p>You're about to delete this data {{ $custodian_id }}</p>
                                                <p>This action cannot be reversed.</p>
                                            </flux:text>
                                        </fieldset>
                                        <div class="flex gap-2">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">Cancel</flux:button>
                                            </flux:modal.close>
                                            <flux:button wire:click='delete({{ $dept->id }}, {{ $contractor->id }})' size='xs' variant="danger">Delete </flux:button>
                                        </div>
                                    </div>
                                </flux:modal>
                                @endforeach
                            </table>
                            @else
                            <p class="bg-gradient-to-r from-rose-500 to-orange-500 bg-clip-text text-xs capitalize font-extrabold text-transparent ...">{{ __('tidak ada kustodian') }}</p>

                            @endif
                        </th>
                    </tr>
                    @empty
                    <tr class="text-center">
                        <th colspan="3" class="text-rose-500 font-semibold">not found !!!</th>
                    </tr>
                    @endforelse

                </tbody>
                <tfoot class="text-center">
                    <tr>
                        <th>#</th>
                        <th colspan="2">{{ __('Kustodian') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-1">{{ $Departments->links() }}</div>
    </x-manhours.layout>
    <flux:modal name="custodian">
        <form wire:submit='store' class='grid justify-items-stretch'>
            @csrf
            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs sm:w-sm border p-4 sm:justify-self-center">
                <legend class="fieldset-legend">{{ $legend }}</legend>
                {{-- Nama Departemen --}}
                <x-label-req>{{ __('Nama Departemen') }} </x-label-req>
                <flux:select size="xs" wire:model.live="department_id" placeholder="Choose Status...">
                    @foreach ($Department as $department)
                    <flux:select.option value="{{ $department->id }}">{{ $department->department_name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <x-label-error :messages="$errors->get('department_id')" />
                {{-- Bussiness Unit --}}
                <x-label-req>{{ __('Bisnis Unit') }} </x-label-req>
                <flux:select size="xs" wire:model.live="contractor_id" placeholder="Choose Status...">
                    @foreach ($Contractors as $contractor)
                    <flux:select.option value="{{ $contractor->id }}">{{ $contractor->contractor_name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <x-label-error :messages="$errors->get('contractor_id')" />
                {{-- Status --}}
                <x-label-req>{{ __('Status') }} </x-label-req>
                <flux:select size="xs" wire:model.live="status" placeholder="Choose Status...">
                    <flux:select.option value="enabled">enabled</flux:select.option>
                    <flux:select.option value="disabled">disabled</flux:select.option>
                </flux:select>
                <x-label-error :messages="$errors->get('status')" />
            </fieldset>

            <div class="modal-action">
                <flux:button size="xs" type="submit" icon="save-icon" variant="primary">Save</flux:button>
                <flux:button size="xs" wire:click='close_modal' icon="close-icon" variant="danger">Close</flux:button>
            </div>
        </form>
    </flux:modal>
    <flux:modal name="custodian-edit">
        <form wire:submit='store' class='grid justify-items-stretch'>
            @csrf
            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs sm:w-sm border p-4 sm:justify-self-center">
                <legend class="fieldset-legend">{{ $legend }}</legend>
                {{-- Nama Departemen --}}
                <x-label-req>{{ __('Nama Departemen') }} </x-label-req>
                <flux:select size="xs" wire:model.live="department_id" placeholder="Choose Status...">
                    @foreach ($Department as $department)
                    <flux:select.option value="{{ $department->id }}">{{$department->department_name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <x-label-error :messages="$errors->get('department_id')" />
                {{-- Bussiness Unit --}}
                <x-label-req>{{ __('Bisnis Unit') }} </x-label-req>
                <flux:select size="xs" wire:model.live="contractor_id" placeholder="Choose Status...">
                    @foreach ($Contractors as $contractor)
                    <flux:select.option value="{{ $contractor->id }}">{{ $contractor->contractor_name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <x-label-error :messages="$errors->get('contractor_id')" />
                {{-- Status --}}
                <x-label-req>{{ __('Status') }} </x-label-req>
                <flux:select size="xs" wire:model.live="status" placeholder="Choose Status...">
                    <flux:select.option value="enabled">enabled</flux:select.option>
                    <flux:select.option value="disabled">disabled</flux:select.option>
                </flux:select>
                <x-label-error :messages="$errors->get('status')" />
            </fieldset>

            <div class="modal-action">
                <flux:button size="xs" type="submit" icon="save-icon" variant="primary">Save</flux:button>
                <flux:button size="xs" wire:click='close_modal' icon="close-icon" variant="danger">Close</flux:button>
            </div>
        </form>
    </flux:modal>

</section>

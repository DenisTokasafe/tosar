 <dialog id="action_modal" class="modal {{ $showActionModal === 'open' ? 'modal-open' : '' }}">
     <div class="modal-box w-11/12 max-w-5xl">
         <form method="dialog">
             <button wire:click="$set('showActionModal', 'close')" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
         </form>

         <h3 class="font-bold text-lg mb-4">{{ __('Manajemen Tindakan Lanjutan') }}</h3>

         <fieldset class="p-3 border rounded-xl border-base-300 bg-base-100">
             <fieldset class="fieldset md:col-span-1" wire:key="field-action">
                 <x-form.label label="Deskripsi Tindakan" required />
                 <div x-data="ckeditorHelper('action_description')" wire:ignore>
                     <div x-ref="editorElement" data-placeholder="Masukkan deskripsi tindakan..."></div>
                 </div>
                 <x-label-error :messages="$errors->get('action_description')" />
             </fieldset>

             <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-3 mt-4">

                 <x-form.tgl label="Batas Waktu" format="d-m-Y" model="action_due_date" :required="true" placeholder="Pilih Tanggal" />
                 <x-form.tgl label="Tanggal Selesai" format="d-m-Y" model="actual_close_date" :required="true" placeholder="Pilih Tanggal" />

                 <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama Pelapor..."

                     modelsearch="searchActResponsibility" modelid="action_responsible_id" {{-- ID asli di DB --}}

                     :options="$pelaporsAct" :showdropdown="$showActPelaporDropdown" {{-- Logic Manual --}} :manualMode="$manualActPelaporMode"

                     manualModelName="manualActPelaporName" enableManualAction="enableManualActPelapor"

                     addManualAction="addActPelaporManual" clickaction="selectActPelapor" />
             </div>

             <div class="flex justify-end mt-4">
                 <flux:button size="xs" wire:click="addAction" variant="primary">
                     {{ __('Tambah ke Daftar') }}
                 </flux:button>
             </div>

             <div class="my-4 divider text-xs">{{ __('Daftar Tindakan Terinput') }}</div>
             <div class="max-h-60 overflow-y-auto">
                 <ul class="space-y-2">
                     @forelse ($actions as $index => $action)
                     <li class="p-2 border rounded-md bg-base-200 flex justify-between items-center">
                         <div>
                             <p class="text-sm font-medium">{!! $action['description'] !!}</p>
                             <p class="text-[10px] opacity-70">Due: {{ $action['due_date'] }} | PIC ID: {{ $action['responsible_id'] }}</p>
                         </div>
                         <flux:button variant="danger" size="xs" wire:click="removeAction({{ $index }})" icon="trash" />
                     </li>
                     @empty
                     <li class="text-center py-4 text-gray-400 text-sm italic border-2 border-dashed rounded-lg">
                         {{ __('Belum ada tindakan.') }}
                     </li>
                     @endforelse
                 </ul>
             </div>
         </fieldset>

         <div class="modal-action">
             <button wire:click="$set('showActionModal', 'close')" class="btn btn-primary btn-sm">Selesai & Tutup</button>
         </div>
     </div>
 </dialog>
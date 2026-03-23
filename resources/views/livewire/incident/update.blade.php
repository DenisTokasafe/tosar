<section class="w-full">
    <x-toast />

    {{-- Breadcrumb khusus Edit --}}
    <div class="flex justify-start mb-2" wire:ignore>
        {{ Breadcrumbs::render('incident-detail', $incidentId) }}
    </div>

    {{-- Header dengan Nomor Laporan --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
        <div>
            <flux:heading level="1" class="mb-1 capitalize">
                {{ __('Update Laporan Insiden') }}
            </flux:heading>
            <flux:subheading size="sm" class="text-accent">
                {{ __('Nomor Laporan:') }} <span class="font-bold text-primary">{{ $report_number }}</span>
            </flux:subheading>
        </div>
        <div class="flex gap-2">
            <span class="badge badge-warning font-bold p-4 shadow-sm italic uppercase tracking-widest text-[10px]">Mode Edit SENTRY</span>
        </div>
    </div>

    <x-incident.layout>
        {{-- Iterasi Collapse (Samakan dengan Create agar UI Konsisten) --}}
        @for ($i = 1; $i <= 3; $i++)
            @php
            $hasErrorInStep=$errors->any() && $this->isFieldInStep($i, $errors->toArray());
            @endphp

            <div
                wire:key="step-edit-container-{{ $i }}"
                class="border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl relative z-0 transition-all duration-300 ease-in-out
                hover:-translate-x-2 hover:shadow-xl hover:z-10
                {{ $hasErrorInStep ? 'border-error shadow-md' : 'hover:border-info' }}">

                {{-- Di Mode Edit, semua step biasanya bisa diklik langsung --}}
                <input type="radio" name="edit-accordion" wire:click="goToStep({{ $i }})" value="{{ $i }}" {{ $currentStep == $i ? 'checked' : '' }} />

                {{-- HEADER COLLAPSE --}}
                <div class="flex items-center justify-between font-semibold collapse-title
                    {{ $hasErrorInStep
                        ? 'bg-error text-error-content animate-pulse'
                        : ($currentStep == $i ? 'bg-linear-to-r from-blue-600 to-info text-white' : 'bg-base-200 text-base-content')
                    }}">

                    <h3 class="flex items-center gap-2 text-sm font-bold tracking-wide uppercase">
                        <span>PART {{ $i }}</span>
                        @if($hasErrorInStep)
                        <span class="text-white border-none badge badge-sm badge-ghost bg-white/20">⚠️ ERROR</span>
                        @endif
                    </h3>
                </div>

                {{-- KONTEN --}}
                <div class="text-xs collapse-content bg-base-100">
                    <div class="pt-4">
                        @if($hasErrorInStep)
                        <div class="p-2 mb-4 text-xs border rounded-lg bg-error/10 text-error border-error/20">
                            <strong>Perhatian:</strong> Perubahan pada Part ini belum valid.
                        </div>
                        @endif

                        {{-- REUSE: Menggunakan partial yang sama dengan Create --}}
                        {{-- Pastikan file partial Anda tidak mengandung logic 'currentStep' yang memblokir input --}}
                        @include('livewire.incident.step_edit.incident-step-' . $i)

                        {{-- NAVIGASI TOMBOL KHUSUS EDIT --}}
                        <div class="flex justify-between pt-4 mt-4 border-t border-base-200">
                            <div>
                                @if($i > 1)
                                <button type="button" wire:click="goToStep({{ $i - 1 }})" class="btn btn-ghost btn-xs">
                                    Kembali ke Part {{ $i - 1 }}
                                </button>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                @if ($i < 9)
                                    <button wire:click="nextStep" class="btn btn-info btn-xs text-white">
                                    Simpan & Lanjut
                                    </button>
                                    @endif

                                    {{-- Tombol Update Utama muncul di setiap step untuk memudahkan user --}}
                                    <button type="button"
                                        wire:click="update"
                                        wire:loading.attr="disabled"
                                        class="btn btn-xs btn-success shadow-md px-4">
                                        <span wire:loading.remove wire:target="update">Update Laporan</span>
                                        <span wire:loading wire:target="update" class="loading loading-spinner loading-xs"></span>
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
    </x-incident.layout>

    @push('scripts')
    <script>
        window.addEventListener('scroll-to-top', event => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Listener tambahan jika ada feedback sukses
        window.addEventListener('alert', event => {
            // Logic tambahan jika perlu
        });
    </script>
    @endpush
</section>
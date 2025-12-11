<div class="flex justify-between">
        <flux:tooltip content="tambah data" position="top">
            <flux:button size="xs" wire:click='open_modal' icon="add-icon" variant="primary"></flux:button>
        </flux:tooltip>
        <div class='md:flex-row flex-col flex gap-2'>
            <flux:input size='xs' icon="magnifying-glass" wire:model.live='search' placeholder="Search Group" />
        </div>
    </div>

<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\Attributes\Modelable;

class SearchSelect extends Component
{
    // Properti input/output utama
    public $modelsearch = '';
    public $modelid = null;

    // Konfigurasi
    public $label;
    public $placeholder = 'Cari...';
    public $columnName = 'name';
    public $required = false;
    public $disabled = false;

    // State Internal
    public $options = [];
    public $showdropdown = false;
    public $manualMode = false;
    public $manualModelName = '';

    // Method untuk mengambil data (DIISI OLEH PARENT)
    // Kita gunakan event atau callback untuk mengambil data dari database
    public function updatedModelsearch()
    {
        if (strlen($this->modelsearch) > 1) {
            // Kita minta data ke parent atau bisa query langsung di sini
            $this->dispatch('request-data', search: $this->modelsearch);
            $this->showdropdown = true;
        } else {
            $this->options = [];
            $this->showdropdown = false;
        }
    }

    public function selectOption($id, $name)
    {
        $this->modelid = $id;
        $this->modelsearch = $name;
        $this->showdropdown = false;
        $this->manualMode = false;

        // Beritahu parent bahwa data telah dipilih
        $this->dispatch('option-selected', id: $id, name: $name);
    }

    public function enableManualMode()
    {
        $this->manualMode = true;
        $this->manualModelName = $this->modelsearch;
        $this->showdropdown = false;
        $this->modelid = null;
    }

    public function render()
    {
        return view('livewire.shared.search-select');
    }
}

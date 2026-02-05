<?php

namespace App\Livewire\Administration\EquipmentMaster;

use Livewire\Component;
use App\Models\Location;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\EquipmentMaster;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EquipmentMasterImport;
use Maatwebsite\Excel\HeadingRowImport;

class Index extends Component
{
    use WithPagination, WithFileUploads;
    public $file_excel;
    public $type, $specific_location, $is_active = true;
    public $technical_data = []; // Untuk menyimpan key-value dinamis (FE No, Capacity, dll)
    public $newKey, $newValue; // Input sementara untuk menambah baris JSON
    public $selected_id, $search;
    public $isEdit = false;
    public $search_area = '';
    // Pilih lokasi
    public $locations = [];
    public $show_location = false;
    public $searchLocation = '';
    public $location_id;
    // cari lokasi
    public $cari_locations = [];
    public $cari_show_location = false;
    public $cari_searchLocation = '';
    public $cari_location_id;

    public $previewData = []; // Untuk menampung data sementara
    public $showPreview = false;

    protected $rules = [
        'type' => 'required',
        'location_id' => 'required|exists:locations,id',
        'technical_data' => 'nullable|array|min:1',
    ];

    // Menambah baris spesifikasi baru (misal: "FE No" -> "PH001")
    public function addTechnicalField()
    {
        if ($this->newKey && $this->newValue) {
            $this->technical_data[$this->newKey] = $this->newValue;
            $this->reset(['newKey', 'newValue']);
        }
    }

    public function removeTechnicalField($key)
    {
        unset($this->technical_data[$key]);
    }

    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')->limit(10)->get();
            $this->show_location = true;
        } else {
            $this->show_location = false;
        }
        $this->reset('location_id');
    }

    public function selectLocation($id, $name)
    {
        $this->location_id = $id;
        $this->searchLocation = $name;
        $this->show_location = false;
    }
    public function updatedCariSearchLocation()
    {
        if (strlen($this->cari_searchLocation) > 2) {
            $this->cari_locations = Location::where('name', 'like', '%' . $this->cari_searchLocation . '%')
                ->orderBy('name')->limit(10)->get();
            $this->cari_show_location = true;
        } else {
            $this->cari_show_location = false;
        };
        $this->reset(['cari_location_id', 'search_area']);
    }

    public function selectCariLocation($id, $name)
    {
        $this->search_area = $name;
        $this->cari_location_id = $id;
        $this->cari_searchLocation = $name;
        $this->cari_show_location = false;
    }

    public function previewExcel()
    {
        $this->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240',
            'type' => 'required',
            'location_id' => 'required',
        ]);

        try {
            // Ambil data hanya dari sheet yang namanya sesuai dengan $this->type
            $importArray = Excel::toArray(
                new EquipmentMasterImport($this->type, $this->location_id),
                $this->file_excel
            );

            // Cari sheet yang namanya cocok (case insensitive)
            // Jika tidak ketemu, Laravel Excel biasanya mengembalikan index 0
            $this->previewData = $importArray[0] ?? [];

            if (empty($this->previewData)) {
                throw new \Exception("Sheet dengan nama '{$this->type}' tidak ditemukan atau kosong.");
            }

            $this->showPreview = true;
        } catch (\Exception $e) {
            $this->dispatch('alert', ['text' => 'Gagal: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function importExcel()
    {
        // Method ini sekarang dipanggil setelah user melihat preview
        try {
            // Karena data sudah divalidasi di preview, langsung jalankan import utama
            // Gunakan ->onlySheets($this->type) agar class import hanya memproses sheet tersebut
            Excel::import(
                new EquipmentMasterImport($this->type, $this->location_id),
                $this->file_excel->getRealPath()
            );

            $this->dispatch('alert', ['text' => 'Data Excel Berhasil Diimport!']);
            $this->reset(['file_excel', 'previewData', 'showPreview']);
        } catch (\Exception $e) {
            $this->dispatch('alert', ['text' => 'Gagal Simpan: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function save()
    {
        $this->validate();

        EquipmentMaster::updateOrCreate(['id' => $this->selected_id], [
            'type' => $this->type,
            'location_id' => $this->location_id,
            'specific_location' => $this->specific_location,
            'technical_data' => $this->technical_data,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('alert', ['text' => $this->isEdit ? 'Data diperbarui!' : 'Data ditambah!']);
        $this->resetForm();
    }

    public function edit($id)
    {
        $data = EquipmentMaster::findOrFail($id);
        $this->selected_id = $id;
        $this->type = $data->type;
        $this->location_id = $data->location_id;
        $this->specific_location = $data->specific_location;
        $this->technical_data = $data->technical_data;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        EquipmentMaster::destroy($id);
        $this->dispatch('alert', ['text' => 'Data berhasil dihapus!']);
    }

    public function resetForm()
    {
        $this->reset(['type', 'location_id', 'specific_location', 'technical_data', 'selected_id', 'isEdit']);
    }
    public function render()
    {
        return view('livewire.administration.equipment-master.index', [
            'equipments' => EquipmentMaster::with('location')
                ->search($this->search)->byArea($this->search_area)
                ->paginate(10),
            'locations' => Location::all(),
            'available_types' => ['Fire Extinguisher', 'Fire Hydrant', 'Fire Hose Reel', 'Fire sprinkler system', 'Ring Buoy', 'Eyewash & Safety Shower', 'Muster Point']
        ]);
    }
    public function paginationView()
    {
        return 'paginate.pagination';
    }
}

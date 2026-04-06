<?php

namespace App\Livewire\Administration\EquipmentMaster;

use Livewire\Component;
use App\Models\Location;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\EquipmentMaster;
use App\Models\InspectionChecklist;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EquipmentMasterImport;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $file_excel;
    public $type, $specific_location, $is_active = true;
    public $technical_data = [];
    public $inputs_req = []; // Tambahkan untuk menyimpan list requirement agar bisa ditandai di Blade
    public $newKey, $newValue;
    public $selected_id, $search;
    public $area;
    public $isEdit = false;
    public $search_area = '';

    public $locations = [];
    public $show_location = false;
    public $searchLocation = '';
    public $location_id;

    public $cari_locations = [];
    public $cari_show_location = false;
    public $cari_searchLocation = '';
    public $cari_location_id;

    public $previewData = [];
    public $showPreview = false;

    protected $rules = [
        'type' => 'required',
        'location_id' => 'required|exists:locations,id',
        'technical_data' => 'nullable|array|min:1',
    ];

    public function updatedType($value)
    {
        $this->generateTechnicalFields();
    }

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
        $this->area = $name;
        $this->show_location = false;
        $this->generateTechnicalFields();
    }

    /**
     * Menggabungkan inputs dan inputs_req dari InspectionChecklist
     * ke dalam form technical_data secara dinamis.
     */
    public function generateTechnicalFields()
    {
        if (!$this->type) return;

        // Jangan override jika sedang edit dan data teknis sudah terisi
        if ($this->isEdit && !empty($this->technical_data)) return;

        $checklist = InspectionChecklist::where('equipment_type', $this->type)
            ->where('location_keyword', $this->area)
            ->first() ?:
            InspectionChecklist::where('equipment_type', $this->type)
            ->where('location_keyword', 'Default')
            ->first();

        if ($checklist) {
            $fields = [];

            // Gabungkan field biasa (inputs) dan field wajib (inputs_req)
            $raw_inputs = is_array($checklist->inputs) ? $checklist->inputs : [];
            $raw_reqs = is_array($checklist->inputs_req) ? $checklist->inputs_req : [];

            // Simpan ke property agar di Blade bisa ditandai (misal: label bintang *)
            $this->inputs_req = $raw_reqs;

            // Satukan semua label dan hapus duplikasi jika ada
            $allLabels = array_unique(array_merge($raw_inputs, $raw_reqs));

            foreach ($allLabels as $label) {
                if (empty($label)) continue;

                // GANTI Karakter khusus dengan underscore agar wire:model lancar
                $safeKey = str_replace([' ', '.', '-', '/'], '_', trim($label));
                $fields[$safeKey] = '';
            }

            $this->technical_data = $fields;
        }
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
            $available_types = [
                'Fire Extinguisher',
                'Fire Hydrant',
                'Fire Hose Reel',
                'Fire sprinkler system',
                'Ring Buoy',
                'Eyewash & Safety Shower',
                'Muster Point',
                'Alarm',
                'First Aid Box'
            ];

            $targetType = htmlspecialchars_decode($this->type);
            $index = array_search($targetType, $available_types);

            if ($index === false) {
                throw new \Exception("Tipe alat '{$targetType}' tidak terdaftar.");
            }

            $importArray = Excel::toArray(
                new EquipmentMasterImport($targetType, $this->location_id),
                $this->file_excel
            );

            $this->previewData = $importArray[$index] ?? [];

            if (empty($this->previewData)) {
                throw new \Exception("Sheet kategori {$targetType} kosong.");
            }

            $this->showPreview = true;
        } catch (\Exception $e) {
            $this->dispatch('alert', ['text' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function importExcel()
    {
        try {
            Excel::import(
                new EquipmentMasterImport(htmlspecialchars_decode($this->type), $this->location_id),
                $this->file_excel->getRealPath()
            );

            $this->dispatch('alert', ['text' => 'Data Excel Berhasil Diimport!']);
            $this->reset(['previewData', 'showPreview']);
        } catch (\Exception $e) {
            $this->dispatch('alert', ['text' => 'Gagal Simpan: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function save()
    {
        $this->validate();

        $cleanTechnicalData = [];

        // Kembalikan underscore menjadi spasi saat simpan ke DB
        foreach ($this->technical_data as $key => $value) {
            $originalKey = str_replace('_', ' ', $key);
            $cleanTechnicalData[$originalKey] = $value;
        }

        EquipmentMaster::updateOrCreate(['id' => $this->selected_id], [
            'type' => $this->type,
            'location_id' => $this->location_id,
            'specific_location' => $this->specific_location,
            'technical_data' => $cleanTechnicalData,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('alert', ['text' => $this->isEdit ? 'Data diperbarui!' : 'Data ditambah!']);
        $this->resetForm();
    }

    public function edit($id)
    {
        $data = EquipmentMaster::whereId($id)->with('location')->first();
        $this->selected_id = $id;
        $this->type = $data->type;
        $this->location_id = $data->location_id;
        $this->specific_location = $data->specific_location;
        $this->searchLocation = $data->location->name;
        $this->isEdit = true;

        $raw_data = is_array($data->technical_data) ? $data->technical_data : [];

        $this->technical_data = [];
        foreach ($raw_data as $key => $value) {
            $safeKey = str_replace([' ', '.', '-', '/'], '_', $key);
            $this->technical_data[$safeKey] = $value;
        }

        // Ambil info requirements untuk menandai field saat edit
        $checklist = InspectionChecklist::where('equipment_type', $this->type)->first();
        $this->inputs_req = ($checklist && is_array($checklist->inputs_req)) ? $checklist->inputs_req : [];
    }

    public function delete($id)
    {
        EquipmentMaster::destroy($id);
        $this->dispatch('alert', ['text' => 'Data berhasil dihapus!']);
    }

    public function resetForm()
    {
        $this->reset(['type', 'technical_data', 'selected_id', 'isEdit', 'inputs_req', 'searchLocation', 'location_id']);
    }

    public function render()
    {
        return view('livewire.administration.equipment-master.index', [
            'equipments' => EquipmentMaster::with('location')
                ->search($this->search)->byArea($this->search_area)
                ->paginate(10),
            'locations' => Location::all(),
            'available_types' => InspectionChecklist::distinct()->pluck('equipment_type'),
        ]);
    }

    public function paginationView()
    {
        return 'paginate.pagination';
    }
}

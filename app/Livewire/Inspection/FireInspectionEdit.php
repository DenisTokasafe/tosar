<?php

namespace App\Livewire\Inspection;

use Livewire\Component;
use App\Models\User;
use App\Models\Location;
use App\Helpers\FileHelper;
use Livewire\WithFileUploads;
use App\Models\FireProtection;
use App\Traits\HasFireInspectionFields;
use Illuminate\Support\Facades\Storage;

class FireInspectionEdit extends Component
{
    use WithFileUploads, HasFireInspectionFields;
    public $inspectionId;
    public $type, $location, $inspection_date, $remarks, $area, $location_id;
    public $conditions = [];
    public $inspected_users = [];
    public $dokumentasi; // File baru yang diupload
    public $old_dokumentasi; // Path file lama dari DB


    // Property untuk Searchable Dropdowns
    public $searchLocation = '';
    public $locations = [];
    public $show_location = false;
    public $searchResponsibility = '';
    public $pelapors = [];
    public $showPelaporDropdown = false;

    public $manualPelaporMode = false;
    public $manualPelaporName = '';
    public $responsible_id;

    public function mount($id)
    {
        $inspection = FireProtection::findOrFail($id);
        $this->inspectionId = $id;

        $this->type = $inspection->type;
        $this->location = $inspection->location;
        $this->area = $inspection->area;
        $this->searchLocation = $inspection->area;
        $this->inspection_date = $inspection->inspection_date;
        $this->remarks = $inspection->remarks;
        $this->conditions = $inspection->conditions;
        $this->old_dokumentasi = $inspection->documentation_path;

        // Load Inspected Users
        if ($inspection->inspected_by) {
            $names = explode('|', $inspection->inspected_by);
            foreach ($names as $name) {
                $this->inspected_users[] = ['id' => null, 'name' => $name];
            }
        }
    }

    public function rules()
    {
        return [
            'location'        => 'required|string|max:255',
            'inspection_date' => 'required|date',
            'inspected_users' => 'required|array|min:1',
            'conditions'      => 'required|array',
            'type'            => 'required|string',
            'area'            => 'required',
            'dokumentasi'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ];
    }

    public function update()
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'location' => $this->location,
            'area' => $this->area,
            'inspection_date' => $this->inspection_date,
            'inspected_by' => implode('|', array_column($this->inspected_users, 'name')),
            'conditions' => $this->conditions,
            'remarks' => $this->remarks,
        ];

        if ($this->dokumentasi) {
            // Hapus file lama jika ada
            if ($this->old_dokumentasi) {
                Storage::disk('public')->delete($this->old_dokumentasi);
            }
            $data['documentation_path'] = FileHelper::compressAndStore($this->dokumentasi, 'inspections/documents');
        }

        FireProtection::find($this->inspectionId)->update($data);

        session()->flash('success', 'Data berhasil diperbarui!');
        return $this->redirect(route('fire-inspection-list'), navigate: true);
    }

        public function updatedSearchResponsibility()
    {
        $this->reset('manualPelaporName');
        $this->manualPelaporMode = false;
        if (strlen($this->searchResponsibility) > 1) {
            $this->pelapors = User::where('name', 'like', '%' . $this->searchResponsibility . '%')
                ->orderBy('name')
                ->limit(50)
                ->get();
            $this->showPelaporDropdown = true;
        } else {
            $this->pelapors = [];
            $this->showPelaporDropdown = false;
        }
    }
    public function selectPelapor($id, $name)
    {
        // $this->searchResponsibility = $name;
        $this->showPelaporDropdown = false;
        $this->manualPelaporMode = false;

        if (!collect($this->inspected_users)->contains('id', $id)) {
            $this->inspected_users[] = [
                'id' => $id,
                'name' => $name
            ];
        }
        $this->reset(['searchResponsibility', 'showPelaporDropdown']);
    }
    public function enableManualPelapor()
    {
        $this->manualPelaporMode = true;
        $this->manualPelaporName = $this->searchResponsibility; // isi default sama dengan isi search
        // Masukkan data manual (id null)
        $this->inspected_users[] = [
            'id' => null,
            'name' => $this->manualPelaporName
        ];
        $this->reset(['manualPelaporName', 'searchResponsibility', 'showPelaporDropdown', 'manualPelaporMode']);
    }
    public function updatedManualPelaporName($value)
    {
        $this->responsible_id = null;
    }
    public function removeInspectedUser($index)
    {
        unset($this->inspected_users[$index]);
        $this->inspected_users = array_values($this->inspected_users); // re-index array
    }

    public function addPelaporManual()
    {
        $this->searchResponsibility = $this->manualPelaporName;
        $this->showPelaporDropdown = false;
        $this->responsible_id = null;
    }

    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(50)
                ->get();
            $this->show_location = true;
        } else {
            $this->locations = [];
            $this->show_location = false;
        }
    }
    public function selectLocation($id, $name)
    {
        $this->location_id = $id;
        $this->searchLocation = $name;
        $this->area = $name;
        $this->show_location = false;
        $this->validateOnly('location_id');
    }

    public function render()
    {
        return view('livewire.inspection.fire-inspection-edit');
    }
}

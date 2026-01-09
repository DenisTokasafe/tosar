<?php

namespace App\Livewire\Wpi;

use Livewire\Component;
use App\Models\WpiReport;
use App\Helpers\FileHelper;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $reportId; // Jika ada ID, berarti mode EDIT
    public $report_date, $report_time, $location, $department;
    public $inspectors = [['name' => '', 'id_number' => '']];
    public $findings = [];

    public function mount($id = null)
    {
        if ($id) {
            $this->loadData($id);
        } else {
            $this->report_date = now()->format('Y-m-d');
            $this->addFinding();
        }
    }
    public function loadData($id)
    {
        $report = WpiReport::with('findings')->findOrFail($id);
        $this->reportId = $report->id;
        $this->report_date = $report->report_date->format('Y-m-d');
        $this->report_time = $report->report_time;
        $this->location = $report->location;
        $this->department = $report->department;
        $this->inspectors = $report->inspectors;

        $this->findings = $report->findings->toArray();
    }

    public function addFinding()
    {
        $this->findings[] = [
            'ohs_risk' => 'L',
            'description' => '',
            'prevention_action' => '',
            'pic_responsible' => '',
            'due_date' => '',
            'new_photos' => [] // Temporary storage untuk upload
        ];
    }

    public function save()
    {
        $this->validate([
            'report_date' => 'required|date',
            'location' => 'required',
            'findings.*.description' => 'required',
        ]);

        // 1. Simpan Header
        $report = WpiReport::updateOrCreate(
            ['id' => $this->reportId],
            [
                'report_date' => $this->report_date,
                'report_time' => $this->report_time,
                'location'    => $this->location,
                'department'  => $this->department,
                'inspectors'  => $this->inspectors,
            ]
        );

        // 2. Simpan Findings (Detail)
        if ($this->reportId) {
            $report->findings()->delete(); // Reset untuk update mudah
        }
        foreach ($this->findings as $finding) {
            $photoPaths = [];

            // Handle Upload Foto menggunakan Helper
            if (!empty($finding['new_photos'])) {
                foreach ($finding['new_photos'] as $photo) {
                    // Menggunakan helper sesuai gambar yang Anda kirim
                    // Parameter: ($file, $folder, $width, $quality)
                    $path = FileHelper::compressAndStore(
                        $photo,
                        'wpi-photos',
                        800, // lebar otomatis resize ke 800px
                        75   // kualitas kompresi 75%
                    );

                    $photoPaths[] = $path;
                }
            }
            $report->findings()->create([
                'ohs_risk' => $finding['ohs_risk'],
                'description' => $finding['description'],
                'prevention_action' => $finding['prevention_action'],
                'pic_responsible' => $finding['pic_responsible'],
                'due_date' => $finding['due_date'],
                'photos' => $photoPaths,
            ]);
        }

        $messages = $this->reportId ? 'Data berhasil di updated' : 'Data berhasil disimpan';

        $this->dispatch('alert', [
            'text'            => $messages,
            'duration'        => 5000,
            'destination'     => '/contact',
            'newWindow'       => true,
            'close'           => true,
            'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
        ]);
        $this->reset('findings');
    }

    public function delete($id)
    {
        WpiReport::find($id)->delete();
        $this->dispatch(
            'alert',
            [
                'text' => "Data berhasil di hapus!!!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "linear-gradient(to right, #ff3333, #ff6666)",
            ]
        );
    }
    public function render()
    {
        return view('livewire.wpi.index');
    }
}

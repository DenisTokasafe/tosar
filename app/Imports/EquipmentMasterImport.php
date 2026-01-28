<?php

namespace App\Imports;

use App\Models\EquipmentMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EquipmentMasterImport implements ToModel, WithHeadingRow, WithMapping
{
    protected $type, $location_id;

    public function __construct($type, $location_id)
    {
        $this->type = $type;
        $this->location_id = $location_id;
    }

    /**
     * Memastikan data dipetakan dengan benar terlepas dari spasi/case di header excel
     */
    public function map($row): array
    {
        // Standarisasi key dari excel (lowercase & replace space dengan underscore)
        $mapped = [];
        foreach ($row as $key => $value) {
            $cleanKey = str_replace(' ', '_', strtolower(trim($key)));
            $mapped[$cleanKey] = $value;
        }
        return $mapped;
    }

    public function model(array $row)
    {
        // Mengambil 'Lokasi Spesifik' dari kolom 'lokasi' (sesuai image_f66506.png)
        $specificLocation = $row['lokasi'] ?? $row['lokasi_spesifik'] ?? $row['location'] ?? null;

        // Data teknis yang akan masuk ke JSON technical_data
        // Menyesuaikan dengan kolom di image_eb5a72.png (FE No, FE Type, Capacity)
        $technicalFields = [
            'FE No'    => $row['fe_no'] ?? null,
            'FE Type'  => $row['fe_type'] ?? null,
            'Capacity' => $row['capacity'] ?? null,
            // Tambahan untuk jenis alat lain seperti Hydrant (Box No) atau Muster Point (ID No)
            'Box No'   => $row['box_no'] ?? null,
            'ID No'    => $row['id_no'] ?? null,
            'ID Muster Point'    => $row['id_muster_point'] ?? null,
            'Hydrant No'    => $row['hydrant_no'] ?? null,
            'E&S No'    => $row['nomor'] ?? null,
            'Hose Reel No'    => $row['hose_reel_no'] ?? null,
            'Sprinkler No'    => $row['sprinkler_no'] ?? null,
            'Ring Buoy No'    => $row['ring_buoy_no'] ?? null,
        ];

        // Filter hanya field yang ada isinya agar JSON bersih
        $technicalData = array_filter($technicalFields, fn($value) => !is_null($value));

        return new EquipmentMaster([
            'type'              => $this->type,
            'location_id'       => $this->location_id,
            'specific_location' => $specificLocation,
            'technical_data'    => $technicalData,
            'is_active'         => true,
        ]);
    }
}

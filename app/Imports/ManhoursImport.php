<?php

namespace App\Imports;

use App\Models\Manhour;
use Maatwebsite\Excel\Concerns\ToModel;

class ManhoursImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Manhour([
            'date'               => $row['date'],
            'company_category'   => $row['company_category'],
            'company'            => $row['company'],
            'department'         => $row['department'],
            'dept_group'         => $row['dept_group'],
            'job_class'          => $row['job_class'],
            'manhours'           => $row['manhours'],
            'manpower'           => $row['manpower'],
        ]);
    }
}

<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Ha van fejléc az Excelben

class StudentsImport implements ToModel, WithHeadingRow
{
    private $classroom_id;

    public function __construct($classroom_id)
    {
        $this->classroom_id = $classroom_id;
    }

    public function model(array $row)
    {

        return new Student([
            'name' => $row['nev'] ?? $row['name'],
            'classroom_id' => $this->classroom_id,
        ]);
    }
}

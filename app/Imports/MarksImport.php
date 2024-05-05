<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use App\Models\Mark;
use App\Models\Models;

class MarksImport implements ToModel
{
    public function model(array $row)
    {
        $mark_id = Mark::where('name', $row[0])->first()->id;

        Models::updateorcreate(['mark_id' => $mark_id, 'name' => $row[1]],
        [
            'mark_id' => $mark_id,
            'start_year' => $row[2],
            'end_year' => $row[3],
            'name' => $row[1]
        ]);

    }
}

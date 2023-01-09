<?php

namespace App\Imports;

use App\Models\ZipCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ZipCodesImport implements WithChunkReading, ToCollection, ShouldQueue, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        //
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function headingRow(): int
    {
        return 1;
    }
}

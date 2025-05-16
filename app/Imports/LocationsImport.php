<?php

namespace App\Imports;

use App\Models\Location;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LocationsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Location([
            'name'       => $row['name'],
            'type_id'    => $row['type_id'],
            'latitude'   => $row['latitude'],
            'longitude'  => $row['longitude'],
            'ip_address' => $row['ip_address'] ?? null,
        ]);
    }
    public function rules(): array
    {
        return [
            '*.name' => 'required',
            '*.type_id' => 'required|exists:types,id',
            '*.latitude' => 'nullable|required|numeric',
            '*.longitude' => 'nullable|required|numeric',
            '*.ip_address' => 'nullable|ip',
        ];
    }
}

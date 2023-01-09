<?php

namespace App\Http\Controllers;

use App\Enums\ZipCodeFiles;
use App\Imports\ZipCodesImport;
use App\Models\ZipCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class ZipCodeController extends Controller
{
    /**
     * Display the specified zip code with addotional info.
     *
     * @param  string  $zipCode
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function show(string $zipCode)
    {
        $zipCodes = ZipCode::where('d_codigo', $zipCode)->get();

        if ($zipCodes->count() > 0) {
            $firstRowData = $zipCodes->first();

            $federalEntity = [];
            $federalEntity['key'] = $firstRowData->c_estado;
            $federalEntity['name'] = $firstRowData->d_estado;
            $federalEntity['code'] = $firstRowData->c_cp;

            $municipality = [];
            $municipality['key'] = $firstRowData->c_mnpio;
            $municipality['name'] = $firstRowData->d_mnpio;

            $settlements = [];
            foreach ($zipCodes as $zipCode) {
                $settlement = [];
                $settlement['key'] = $zipCode->id_asenta_cpcons;
                $settlement['name'] = $zipCode->d_asenta;
                $settlement['zone_type'] = $zipCode->d_zona;
                $settlement['settlement_type'] = [
                    'name' => $zipCode->d_tipo_asenta,
                ];

                array_push($settlements, $settlement);
            }

            $obj = [];
            $obj['zip_code'] = $firstRowData->d_codigo;
            $obj['locality'] = $firstRowData->d_ciudad;
            $obj['federal_entity'] = $federalEntity;
            $obj['settlements'] = $settlements;
            $obj['municipality'] = $municipality;

            return response()->json($obj);
        }

        return response()->json($zipCodes);
    }
}

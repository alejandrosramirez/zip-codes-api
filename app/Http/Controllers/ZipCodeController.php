<?php

namespace App\Http\Controllers;

use App\Enums\ZipCodeFiles;
use App\Imports\ZipCodesImport;
use App\Models\ZipCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

ini_set('memory_limit', '768M');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

class ZipCodeController extends Controller
{
    /**
     * Save a zip codes file to the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {

        /**
         * El parametro entra en un switch case para verificar que la entrada de la request
         * es valida y es un archivo valido, de lo contrario notifica un error y señala las opciones disponibles.
         *
         * Archivos disponibles: 'ags-chih', 'df-mich', mor-slp', 'sin-zac'
         *
         * Nota: Solo se especifican las opciones disponibles sin la extensión .xls
         */
        switch ($request->input('zip_code_file')) {
            case ZipCodeFiles::AGS_CHIH->value:
                break;
            case ZipCodeFiles::DF_MICH->value:
                break;
            case ZipCodeFiles::MOR_SLP->value:
                break;
            case ZipCodeFiles::SIN_ZAC->value:
                break;
            default:
                $fileNames = '';
                foreach (ZipCodeFiles::cases() as $case) {
                    $fileNames .= $case->value . '.xls, ';
                }

                throw new Exception('El archivo ' . $request->input('zip_code_file') . '.xls no existe. Solo se admiten los siguientes archivos: ' . $fileNames);
        }

        /**
         * Si todo fue correcto procedemos a leer el archivo dentro de nuestra carpeta pública
         * y procedemos a leer las hojas de cada archivo y a insertar los registros en nuestra tabla 'zip_codes'
         */
        $fileSheets = Excel::toCollection(new ZipCodesImport(), public_path('zip_codes/' . $request->input('zip_code_file') . '.xls'));

        $stateInfo = [];
        if (count($fileSheets) > 0) {
            $rowsNo = 0;
            foreach ($fileSheets as $sheet) {
                $firstRowData = $sheet->first();

                array_push($stateInfo, $firstRowData['d_estado']);

                foreach ($sheet as $line) {
                    $line = $line->toArray();

                    ZipCode::create([
                        'd_codigo' => $line["d_codigo"],
                        'd_asenta' => $line["d_asenta"],
                        'd_tipo_asenta' => $line["d_tipo_asenta"],
                        'd_mnpio' => $line["d_mnpio"],
                        'd_estado' => $line["d_estado"],
                        'd_ciudad' => isset($line["d_ciudad"]) ? $line["d_ciudad"] : null,
                        'd_cp' => $line["d_cp"],
                        'c_estado' => $line["c_estado"],
                        'c_oficina' => $line["c_oficina"],
                        'c_cp' => isset($line["c_cp"]) ? $line["c_cp"] : null,
                        'c_tipo_asenta' => $line["c_tipo_asenta"],
                        'c_mnpio' => $line["c_mnpio"],
                        'id_asenta_cpcons' => $line["id_asenta_cpcons"],
                        'd_zona' => isset($line["d_zona"]) ? $line["d_zona"] : null,
                        'c_cve_ciudad' => isset($line["c_cve_ciudad"]) ? $line["c_cve_ciudad"] : null,
                    ]);

                    $rowsNo++;
                }
            }
        }

        /**
         * Retornamos una respuesta en json que indica que archivo se guardo correctamente
         * los registros insertados y esecificamente de que estados. :)
         */
        return response()->json([
            'message' => 'Archivo ' . $request->input('zip_code_file') . '.xls importado correctamente.',
            'data' => [
                'rows' => 'Se insertaron ' . $rowsNo . ' filas.',
                'states_imported' => $stateInfo,
            ],
        ]);
    }

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

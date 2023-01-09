<?php

namespace App\Console\Commands;

use App\Enums\ZipCodeFiles;
use App\Imports\ZipCodesImport;
use App\Models\ZipCode;
use Exception;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ZipCodeImpoter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zipcode:importer {--file=* : The alias of the file, example: a, b, c, d, e, f, g or h}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load a zip code file and store it to the database.';

    /**
    * Create a new command instance.
    *
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('memory_limit', '768M');
        ini_set('max_execution_time', 300);

        $file = $this->option('file')[0];

        /**
         * El parametro entra en un switch case para verificar que la entrada de la request
         * es valida y es un archivo valido, de lo contrario notifica un error y señala las opciones disponibles.
         *
         * Archivos disponibles: 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'
         *
         * Nota: Solo se especifican las opciones disponibles sin la extensión .xls
         */
        switch ($file) {
            case ZipCodeFiles::a->value:
                break;
            case ZipCodeFiles::b->value:
                break;
            case ZipCodeFiles::c->value:
                break;
            case ZipCodeFiles::d->value:
                break;
            case ZipCodeFiles::e->value:
                break;
            case ZipCodeFiles::f->value:
                break;
            case ZipCodeFiles::g->value:
                break;
            case ZipCodeFiles::h->value:
                break;
            default:
                $fileNames = '';
                foreach (ZipCodeFiles::cases() as $case) {
                    $fileNames .= $case->value . ', ';
                }

                throw new Exception('El archivo ' . $file . ' no existe. Solo se admiten los siguientes archivos: ' . $fileNames);
        }

        /**
         * Si todo fue correcto procedemos a leer el archivo dentro de nuestra carpeta pública
         * y procedemos a leer las hojas de cada archivo y a insertar los registros en nuestra tabla 'zip_codes'
         */
        $fileSheets = Excel::toCollection(new ZipCodesImport(), public_path('zip_codes/' . $file . '.xls'));

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

        $this->info('Archivo ' . $file . '.xls importado correctamente.');
        $this->info('Se insertaron ' . $rowsNo . ' filas.');
        $this->info(implode(', ',  $stateInfo));

        return Command::SUCCESS;
    }
}

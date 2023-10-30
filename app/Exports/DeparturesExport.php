<?php

namespace App\Exports;

use App\Http\Resources\Departure\DepartureExportResource;
use App\Models\Client;
use App\Models\Departure;
use App\Models\Passport;
use App\Services\DepartureService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use phpseclib3\Math\PrimeField\Integer;


class DeparturesExport implements WithMapping, FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;

    private $id;

    private $departure;

    public function __construct($id)
    {
        $this->id = $id;
        $d = new DepartureExportResource(Departure::find($this->id));
        $this->departure = $d;
        return $this;
    }

    public function collection()
    {
        $comparacion = 1;
        $contador = 0;

        $blankDeparture = new Departure();

        // TODO: aqui necesitaremos datos de departure y departure client (lo que era el traveler y departure)

        $value = collect($this->departure->active);

        /*$departure = Departure::find($this->departure);//new DepartureExportResource(Departure::find($this->departure));
        Log::debug(json_encode($departure));

        // Clientes  que han pagado?
        $value = $departure->active;


        // Clientes en espera (->clientExports() = DepartureClient)
        $waiting = $departure->waiting;

        // Para crear rows vacias? Eso parece, las mete para separasr los usuarios apuntados de los que estan en espera
        $value->add(new Departure);
        $value->add(new Departure);
        $value->add(new Departure);
        $value->add(new Departure);

        // Añade los usuarios en espera (podria ser un array_merge())
        foreach ($waiting as $listuser) {
            $value->push($listuser);
        }

        $indices = array();

        // Indices donde debemos insertar una fila en blanco
        foreach ($value as $key => $departure) {
            if (isset($departure->number_room)) {
                if ($comparacion <= $departure->number_room) {
                    $comparacion = $comparacion + 1;
                    array_push($indices, $key);
                }
            }
        }

        // insertamos la fila en blacno
        foreach ($indices as $ind) {
            $value->splice($ind + $contador, 0, [$blankDeparture]);
            $contador = $contador + 1;
        }*/

        return $value;
    }


    public function map($row): array
    {
        dd($row);

        if (isset($row->type_room) && $row->type_room == 1) {
            // $typeroom = "individual";
            $typeroom = "DUI";
        }
        if (isset($row->type_room) && $row->type_room == 2) {
            $typeroom = "doble";
        }
        if (isset($row->type_room) && $row->type_room == 3) {
            $typeroom = "twins";
        }
        if (isset($row->type_room) && $row->type_room == 4) {
            $typeroom = "triple";
        }

        if (isset($row->number_room)) {
            $numberRoom = $row->number_room;
        } else if (!isset($row->number_room)  &&  isset($row->type_room)) {
            $numberRoom = "Lista de espera";
        } else {
            $numberRoom = "";
        }

        if (isset($row->state)) {
            $stado = $row->state;
        } else {
            $stado = 0;
        }


        $Departure = [
            'trip_id'               => 'departures.trip_id',
            'state_id'              => 'departures.state',
            'start'                 => 'departures.start',
            'final'                 => 'departures.final',
            'price'                 => 'departures.price',
            'taxes'                 => '', // Viene de que edn algunas dentro de price tiene cosas delñ estilo + tasas o + taxes pfff
            'individual_supplement' => 'departures.individual_supplement',
            'pax_capacity'          => 'departures.pax_available',
            'commentary'            => 'departures.commentary',
            'expedient'             => 'departures.expedient',
            'created_at'            => 'departures.created_at',
            'updated_at'            => 'departures.updated_at',
            'deleted_at'            => 'departures.deleted_at',
        ];

        $Room = [
            'room_type_id'  => 'rooms.type_room',
            'departure_id'  => 'rooms.departure_id',
            'room_number'   => 'rooms.number_room',
            'observations'  => 'rooms.observations',
            'created_at'    => 'rooms.created_at',
            'updated_at'    => 'rooms.updated_at',
            'deleted_at'    => null,
        ];


        // Las que no viene de row es que las ha calculado antges, ahora lo tenemos todo en DB
        return [


            $numberRoom ?? "",                      // Room.room_number
            $stado,                                 // ClientDeparture.state
            $row->surname ?? "",                    // Client.surname
            $row->name ?? "",                       // Client.name
            $typeroom ?? "",                        // El nombre del tipo de  habitacion?
            $row->type_room ?? "",                  // El ID del tipo de habitacion?
            $row->phone ?? "",                      // Client.phone
            $row->email ?? "",                      // Client.email
            $row->seat ?? "",                       // ClientDeparture.seat
            $row->rm_observations ?? "",            // Room.observations
            $row->intolerances ?? "",               // Client.intolerances
            $row->dni ?? "",                        // Client.dni
            $row->dni_expiration ?? "",             // Client.dni_expiration
            $row->number_passport ?? "",            // Passport.number_passport
            $row->issue ?? "",                      // Passport.issue
            $row->exp ?? "",                        // Passport.exp
            $row->place_birth ?? "",                // Client.place_birth
            $row->birth ?? "",                      // Client.birth
            $row->nacionallity ?? "",              // Passport.nacianallity
            $row->observations ?? "",               // DepartureClient.observatrions



        ];
    }




    public function headings(): array
    {
        $departure = new DepartureExportResource(Departure::find($this->departure)->first());

        return [

            [

                $departure->title . ' - del ' .  date('d-m-Y', strtotime($departure->start))  . ' al ' .  date('d-m-Y', strtotime($departure->final)),

            ],

            [

                $departure->clients()->distinct('room_number')->count('room_number') . ' HABITACIONS: ' .

                    $departure->clients()->distinct('room_number')->where('type_room', 1)->count('room_number') . ' Individuals    ' .

                    $departure->clients()->distinct('room_number')->where('type_room', 2)->count('room_number') . ' Dobles    ' .

                    $departure->clients()->distinct('room_number')->where('type_room', 3)->count('room_number') . ' Twins' .

                    '        PAX TOTAL   ' . $departure->clients()->where('state', '<', 4)->count()
            ],

            [
                "Comentarios viaje: " . $departure->description
            ],
            [
                "Comentarios salida: " . $departure->commentary
            ],
            [],

            [
                "hab",
                __('state'),
                __('Surname'),
                __('Name'),
                "Tipo hab",
                "Observaciones hab",
                __('Phone'),
                __('Mail'),
                __('Seat'),
                __('Observations Generals'),
                __('Intolerances'),
                __('Dni'),
                __('Dni expiration'),
                "Pastport N",

                __('Issue'),
                "EXP",
                "DOB",

                "POB",
                __('Nationality'),
                __('Observations Punctual'),

            ],

        ];
    }





    public function registerEvents(): array
    {


        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });

        Sheet::macro('setOrientation', function (Sheet $sheet, $orientation) {
            $sheet->getDelegate()->getPageSetup()->setOrientation($orientation);
        });

        Sheet::macro('setFontTitle', function (Sheet $sheet, string $cellRange, $size) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($size);
        });

        Sheet::macro('setFontSubTitle', function (Sheet $sheet, string $cellRange, $size) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($size);
        });

        Sheet::macro('mergeCell', function (Sheet $sheet, string $cellRange) {
            $sheet->getDelegate()->mergeCells($cellRange);
        });

        Sheet::macro('fillCell', function (Sheet $sheet, string $cellRange, $fill) {
            $sheet->getDelegate()->getStyle($cellRange)->getFill()->applyFromArray($fill);
        });

        Sheet::macro('setFontHeader', function (Sheet $sheet, string $cellRange, $size) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($size);
        });

        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });


        return
            [
                //Despues de crear la hoja
                AfterSheet::class    => function (AfterSheet $event) {

                    //ALINEAR
                    // $event->sheet->styleCells(
                    //     'C2:C1000',
                    //     [
                    //         'alignment' => [
                    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    //         ],
                    //     ]
                    // );

                    $rows = $event->sheet->getDelegate()->toArray();

                    $index = 1;
                    foreach ($rows as $k => $v) {


                        if ($v[1] == 1.0) {

                            $event->sheet->fillCell(
                                'B' . $index,

                                ['fillType' => Fill::FILL_SOLID, 'color' => array('rgb' => 'e63434')]
                            );
                        } elseif ($v[1] == 2.0) {
                            $event->sheet->fillCell(
                                'B' . $index,

                                ['fillType' => Fill::FILL_SOLID, 'color' => array('rgb' => '2de90a')]
                            );
                        } elseif ($v[1] == 3.0) {
                            $event->sheet->fillCell(
                                'B' . $index,

                                ['fillType' => Fill::FILL_SOLID, 'color' => array('rgb' => '343ae6')]
                            );
                        } else { }
                        $index++;
                    }


                    // dd("d");
                    $event->sheet->fillCell(
                        'A6:S6',

                        ['fillType' => Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFF00')]
                    );



                    $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


                    $event->sheet->setFontTitle(
                        'A1',
                        [
                            'font' => [
                                'size' => 20,
                                'bold' => true
                            ],


                        ]

                    );


                    $event->sheet->setFontSubTitle(
                        'A2',
                        [
                            'font' => [
                                'size' => 12,
                                'bold' => true,
                                'color' => [
                                    'argb' => 'FFFF0000'
                                ]
                            ]
                        ]

                    );



                    $event->sheet->setFontHeader(
                        'A3:O3',
                        [
                            'font' => [
                                'size' => 12,
                                'bold' => true,
                            ]
                        ]

                    );

                    $event->sheet->mergeCell(
                        'A1:S1'
                    );

                    $event->sheet->mergeCell(
                        'A2:S2'
                    );
                    $event->sheet->mergeCell(
                        'A3:S3'
                    );
                    $event->sheet->mergeCell(
                        'A4:S4'
                    );
                    $event->sheet->mergeCell(
                        'A5:S5'
                    );
                },
            ];
    }


    public function title(): string
    {
        $departure = Departure::find($this->departure)->first();
        // return  $departure->trip->title .' '. $departure->start;
        return 'EXP' . $departure->expedient;
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER,


        ];
    }
}

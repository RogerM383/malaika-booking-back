<?php

namespace App\Exports;
;

use App\Http\Resources\Departure\DepartureExportResource;
use App\Models\Departure;
use GuzzleHttp\Psr7\LazyOpenStream;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
use function Psy\debug;


class DeparturesExport2 implements WithMapping, FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;

    public $departure;

    public function __construct($departure)
    {
        $this->departure = $departure;
        return $this;
    }


    /*public function collection()
    {
        $comparacion = 1;
        $contador = 0;

        $empty = [
            'id'             => null,
            'room_number'    => null,
            'state'          => null,
            'surname'        => null,
            'name'           => null,
            'type_room_id'   => null,
            'type_room'      => null,
            'phone'          => null,
            'email'          => null,
            'seat'           => null,
            'rm_observations'=> null,
            'intolerances'   => null,
            'dni'            => null,
            'dni_expiration' => null,
            'number_passport'=> null,
            'issue'          => null,
            'exp'            => null,
            'place_birth'    => null,
            'birth'          => null,
            'nationality'    => null,
            'dp_observations'=> null,
        ];

        $departure = new DepartureExportResource(Departure::find($this->departure));
        $active = $departure->toArray(null)['active'];
        $waiting =  $departure->toArray(null)['waiting'];

        $value = $active;

        $value->add($empty);
        $value->add($empty);
        $value->add($empty);
        $value->add($empty);

        // Añade los usuarios en espera (podria ser un array_merge())
        foreach ($waiting as $listuser) {
            $value->push($listuser);
        }

        // Indices donde debemos insertar una fila en blanco
        foreach ($value as $key => $departure) {
            if (isset($departure->number_room)) {
                if ($comparacion <= $departure->number_room) {
                    $comparacion = $comparacion + 1;
                    array_push($indices, $key);
                }
            }
        }

        $indices = array();

        // insertamos la fila en blacno
        foreach ($indices as $ind) {
            $value->splice($ind + $contador, 0, [$empty]);
            $contador = $contador + 1;
        }

        return $value;
    }*/

    public function collection()
    {
        $comparacion = 1;
        $contador = 0;

        $empty = [
            'id'             => null,
            'room_number'    => null,
            'state'          => null,
            'surname'        => null,
            'name'           => null,
            'room_type_id'   => null,
            'type_room'      => null,
            'phone'          => null,
            'email'          => null,
            'seat'           => null,
            'rm_observations'=> null,
            'intolerances'   => null,
            'dni'            => null,
            'dni_expiration' => null,
            'number_passport'=> null,
            'issue'          => null,
            'exp'            => null,
            'place_birth'    => null,
            'birth'          => null,
            'nationality'    => null,
            'dp_observations'=> null,
        ];

        /*$data = new DepartureExportResource(Departure::find($this->departure)->first());
        $json = json_decode($data->toJson());

        $active = array_values(Arr::sort($json->active, function ($value) {
            return $value->room_number;
        }));

        $waiting = array_values(Arr::sort($json->waiting, function ($value) {
            return $value->room_number;
        }));*/

        $departure = new DepartureExportResource(Departure::find($this->departure));
        $departure = json_decode($departure->toJson());

        //Log:debug($departure);

        $active = $departure->active;
        $waiting = $departure->waiting;


        $value = collect($active)->sortBy('room_number')->values();


        $value->add($empty);
        $value->add($empty);
        $value->add($empty);
        $value->add($empty);

        // Añade los usuarios en espera (podria ser un array_merge())
        foreach ($waiting as $listuser) {
            $value->push($listuser);
        }

        $indices = array();

        // Indices donde debemos insertar una fila en blanco
        foreach ($value as $key => $departure) {

            if (isset($departure->room_number)) {
                if ($departure->room_number > $comparacion) {
                    $value->splice($key + $contador, 0, [$empty]);
                    $comparacion = $departure->room_number;
                    $contador++;
                }
                //if ($comparacion <= $departure->room_number) {
                //    $comparacion = $comparacion + 1;
                //    //array_push($indices, $key + 1);
                //}
            }
        }

        // insertamos la fila en blacno
        //foreach ($indices as $ind) {
        //    $value->splice($ind + $contador, 0, [$empty]);
        //    $contador = $contador + 1;
        //}

        return $value;
    }

    public function map($row): array
    {
        /*if (isset($row->type_room) && $row->type_room == 1) {
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
        }*/

        if (isset($row->room_number)) {
            $roomRumber = $row->room_number;
        } else if (!isset($row->room_number) && isset($row->type_room) ) {
            $roomRumber = "Llista de espera";
        } else {
            $roomRumber = "";
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
            //'observations'  => 'rooms.observations',
            'created_at'    => 'rooms.created_at',
            'updated_at'    => 'rooms.updated_at',
            'deleted_at'    => null,
        ];

        // Las que no viene de row es que las ha calculado antges, ahora lo tenemos todo en DB
        return [
            $roomRumber ?? "",                      // Room.room_number
            $stado,                                 // ClientDeparture.state
            $row->surname ?? "",                    // Client.surname
            $row->name ?? "",                       // Client.name
            $row->type_room ?? "",                  // El nombre del tipo de  habitacion?
            $row->rm_observations ?? "",            // Room.observations
            //$row->type_room ?? "",                // El ID del tipo de habitacion?
            $row->phone ?? "",                      // Client.phone
            $row->email ?? "",                      // Client.email
            $row->seat ?? "",                       // ClientDeparture.seat
            $row->dp_observations ?? "",            // Deparrtreu observations  ---  Room.observations
            $row->intolerances ?? "",               // Client.intolerances
            $row->dni ?? "",                        // Client.dni
            $row->dni_expiration ?? "",             // Client.dni_expiration
            $row->number_passport ?? "",            // Passport.number_passport
            $row->issue ?? "",                      // Passport.issue
            $row->exp ?? "",                        // Passport.exp
            $row->birth ?? "",                      // Client.birth
            $row->place_birth ?? "",                // Client.place_birth
            $row->nationality ?? "",                // Passport.nationality
            $row->notes ?? "",                      // Client.notes
        ];
    }




    public function headings(): array
    {
        $departure = new DepartureExportResource(Departure::find($this->departure));
        $departure = json_decode($departure->toJson());

        $types = $departure->room_types;
        $arr = array_map(function ($type) {
            return ['name' => $type->name, 'quantity' => $type->pivot->quantity];
        }, $types);

        $total = array_sum(array_column($arr, 'quantity'));

        $str = $total . ' HABITACIONS:    ';
        foreach ($arr as $type) {
            $str = $str . $type['quantity'] . ' ' . $type['name'] . '    ';
        }
        $str = $str . '        PAX TOTAL   ' . count($departure->active);

        return [
            [
                $departure->trip_title . ' - del ' .  date('d-m-Y', strtotime($departure->start))  . ' al ' .  date('d-m-Y', strtotime($departure->final)),
            ],
            [
                $str
            ],

            [
                "Comentarios viaje: " //. $departure->description
            ],
            [
                "Comentarios salida: " . $departure->commentary
            ],
            [

            ],
            [
                "hab",
                'Estat',
                'Cognom',
                'Nom',
                "Tipo hab",
                "Observacions hab",
                'Telefon',
                'Email',
                'Seient',
                'Observations Generals',
                'Intolerancies',
                'Dni',
                'Data de caducitat',
                "Numero passaport",
                'Issue',
                "EXP",
                "DOB",
                "POB",
                'Nacionalitat',
                'Observacions Puntuals',
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


                        if ($v[1] == 2.0) {

                            $event->sheet->fillCell(
                                'B' . $index,

                                ['fillType' => Fill::FILL_SOLID, 'color' => array('rgb' => 'e63434')]
                            );
                        } elseif ($v[1] == 3.0) {
                            $event->sheet->fillCell(
                                'B' . $index,

                                ['fillType' => Fill::FILL_SOLID, 'color' => array('rgb' => '2de90a')]
                            );
                        } elseif ($v[1] == 4.0) {
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
        return 'Hola';
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}

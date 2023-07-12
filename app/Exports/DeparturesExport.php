<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Departure;
use App\Models\Passport;
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


class DeparturesExport implements WithMapping, FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;

    public Departure $departure;

    /**
     * @param $departure
     */
    public function __construct($departure)
    {
        $this->departure = $departure;
        return $this;
    }



    public function collection()
    {
        $comparacion = 1;
        $contador = 0;

        $blankDeparture = new Client;

        $value = Departure::find($this->departure)->clientsExports()->wherePivot('state', '<', 4)->orderBy('rel_departure_client.number_room', 'asc')->get();

        $waiting = Departure::find($this->departure)->clientsExports()->where('state', 5)->orderBy('rel_departure_client.updated_at', 'asc')->get();

        $value->add(new Client);
        $value->add(new Client);
        $value->add(new Client);
        $value->add(new Client);

        foreach ($waiting as $listuser) {
            $value->push($listuser);
        }

        //$mergedCollection= $value->merge($waiting);

        $indices = array();

        // Indices donde debemos insertar una fila en blanco
        foreach ($value as $key => $departure) {
            if (isset($departure->pivot->number_room)) {

                if ($comparacion <= $departure->pivot->number_room) {
                    $comparacion = $comparacion + 1;
                    array_push($indices, $key);
                }
            }
        }


        // insertamos la fila en blacno
        foreach ($indices as $ind) {
            $value->splice($ind + $contador, 0, [$blankDeparture]);
            $contador = $contador + 1;
        }


        return $value;
    }



    public function map($row): array
    {

        // dump($row);

        if (isset($row->pivot->type_room) && $row->pivot->type_room == 1) {
            // $typeroom = "individual";
            $typeroom = "DUI";
        }
        if (isset($row->pivot->type_room) && $row->pivot->type_room == 2) {
            $typeroom = "doble";
        }
        if (isset($row->pivot->type_room) && $row->pivot->type_room == 3) {
            $typeroom = "twins";
        }
        if (isset($row->pivot->type_room) && $row->pivot->type_room == 4) {
            $typeroom = "triple";
        }

        if (isset($row->pivot->number_room)) {
            $numberRoom = $row->pivot->number_room;
        } else if (!isset($row->pivot->number_room)  &&  isset($row->pivot->type_room)) {
            $numberRoom = "Lista de espera";
        } else {
            $numberRoom = "";
        }



        if (isset($row->pivot->state)) {
            $stado = $row->pivot->state;
        } else {
            $stado = 0;
        }



        return [


            $numberRoom ?? "",
            $stado,
            $row->surname ?? "",
            $row->name ?? "",
            $typeroom ?? "",
            $row->traveler->type_room ?? "",
            $row->phone ?? "",
            $row->email ?? "",
            $row->traveler->seat ?? "",
            $row->traveler->observations ?? "",
            $row->traveler->intolerances ?? "",
            $row->dni ?? "",
            $row->dni_expiration ?? "",
            $row->passport->number_passport ?? "",
            $row->passport->issue ?? "",
            $row->passport->exp ?? "",
            $row->passport->birth ?? "",
            $row->place_birth ?? "",
            $row->passport->nac ?? "",
            $row->pivot->observations ?? "",



        ];
    }




    public function headings(): array
    {
        $departure = Departure::find($this->departure);

        return [

            [

                $departure->trip->title . ' - del ' .  date('d-m-Y', strtotime($departure->start))  . ' al ' .  date('d-m-Y', strtotime($departure->final)),

            ],

            [

                $departure->clients()->distinct('number_room')->count('number_room') . ' HABITACIONS: ' .

                    $departure->clients()->distinct('number_room')->where('type_room', 1)->count('number_room') . ' Individuals    ' .

                    $departure->clients()->distinct('number_room')->where('type_room', 2)->count('number_room') . ' Dobles    ' .

                    $departure->clients()->distinct('number_room')->where('type_room', 3)->count('number_room') . ' Twins' .

                    '        PAX TOTAL   ' . $departure->clients()->where('state', '<', 4)->count()
            ],

            [
                "Comentarios viaje: " . $departure->trip->description
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
        $departure = Departure::find($this->departure);
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

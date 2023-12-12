<?php
namespace App\Traits;

use Illuminate\Support\Carbon;

const MESOS = [
    'Gener',
    'Febrer',
    'Març',
    'Abril',
    'Maig',
    'Juny',
    'Juliol',
    'Agost',
    'Setembre',
    'Octubre',
    'Novembre',
    'Desembre'
];

trait HandleDates
{
    function getPeriod ($start, $end)
    {
        $_start = Carbon::createFromDate($start);
        $_end = Carbon::createFromDate($end);
        if ($_start->month === $_end->month) {
            return 'del '.$_start->day.' al '.$_end->day.' de '.MESOS[$_start->month-1];
        } else {
            return 'del '.$_start->day.' de '.MESOS[$_start->month-1].' al '.$_end->day.' de '.MESOS[$_end->month-1];
        }
    }
}

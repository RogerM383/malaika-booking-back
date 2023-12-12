<?php
namespace App\Traits;

use Illuminate\Support\Carbon;

trait HandleDates
{
    function getPeriod ($start, $end)
    {
        $_start = Carbon::createFromDate($start);
        $_end = Carbon::createFromDate($end);
        if ($_start->month === $_end->month) {
            return 'del '.$_start->day.' al '.$_end->day.' de '.$_start->month;
        } else {
            return 'del '.$_start->day.' de '.$_start->month.' al '.$_end->day.' de '.$_end->month;
        }
    }
}

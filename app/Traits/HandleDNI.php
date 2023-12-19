<?php
namespace App\Traits;

trait HandleDNI
{
    function trimDNI($str): string
    {
        $alphaNum = preg_replace('/[^a-zA-Z0-9]/','', $str);
        return strtoupper($alphaNum);
    }
}

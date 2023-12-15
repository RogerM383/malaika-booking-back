<?php
namespace App\Traits;

trait HandleDNI
{
    function trimDNI($cadena) {
        // Eliminar espacios y guiones
        $cadenaSinEspacios = str_replace([' ', '-'], '', $cadena);

        // Convertir a mayúsculas
        $cadenaMayusculas = strtoupper($cadenaSinEspacios);

        return $cadenaMayusculas;
    }
}

<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

trait HandleFiles
{
    private function isBase64Image($base64String)
    {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64String));
        // Verifica si la decodificación fue exitosa y comienza con el marcador de imagen
        return ($imageData !== false) && (strpos($imageData, "\xFF\xD8") === 0 || strpos($imageData, "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") === 0);
    }

    private function isBase64Pdf($base64String)
    {
        $pdfData = base64_decode(preg_replace('#^data:application/pdf;base64,#i', '', $base64String));
        // Verifica si la decodificación fue exitosa y comienza con el marcador de PDF
        return ($pdfData !== false) && (substr($pdfData, 0, 4) === "%PDF");
    }

    public function storeBase64File($base64File, $filename, string $type = 'image', string $folder = 'images')
    {
        preg_match('#^data:(\w+)/(\w+);base64,#i', $base64File, $matches);

        $mime = $matches[1];
        $extension = $matches[2];

        // decode the base64 file
        $fileData = base64_decode(preg_replace('#^data:'.$mime.'/\w+;base64,#i', '', $base64File));

        return Storage::disk('public')->put( $folder . '/' . $filename . '.' . $extension, $fileData);
    }
}

<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
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

    public function storeBase64File($base64File, $filename, string $type = 'image', string $folder = 'images')
    {
        // decode the base64 file
        $fileData = base64_decode(preg_replace('#^data:'.$type.'/\w+;base64,#i', '', $base64File));

        // save it to temporary dir first.
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $fileData);

        // this just to help us get file info.
        $tmpFile = new File($tmpFilePath);

        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );

        $extension = explode('/', $file->getMimeType() )[1];
        $filename = $filename . '.' . $extension;
        return $file->move($folder.'/', $filename);
    }
}

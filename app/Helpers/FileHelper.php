<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class FileHelper
{
    public static function compressAndStore($file, $folder, $width = 800, $quality = 75)
    {
        $filename  = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $path      = $folder . '/' . $filename;

        $manager = new ImageManager(
            new Driver()
        ); // pilih driver // atau 'imagick'

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            // Resize + kompres
            $image = $manager->read($file->getRealPath())
                ->scale(width: $width) // resize ke lebar tertentu
                ->encodeByExtension($extension, quality: $quality);

            Storage::disk('public')->put($path, (string) $image);
        } else {
            // kalau bukan gambar (misal PDF), simpan langsung
            $path = $file->storeAs($folder, $filename, 'public');
        }

        return $path;
    }
}

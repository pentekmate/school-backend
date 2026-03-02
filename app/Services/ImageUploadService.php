<?php

namespace App\Services;

use App\Jobs\ProcessImage;
use Illuminate\Http\UploadedFile;

class ImageUploadService
{
    // Eredeti fájl feltöltés
    public function store(UploadedFile $file): string
    {
        $path = $file->store('group-items/original', 'public');

        ProcessImage::dispatch($path);

        return $path;
    }

    public function storeBase64(string $base64Image): string
    {
        [$type, $data] = explode(';', $base64Image);
        [$discard, $data] = explode(',', $data);
        $decoded = base64_decode($data);

        $extension = match (true) {
            str_contains($type, 'jpeg') => 'jpg',
            str_contains($type, 'png') => 'png',
            str_contains($type, 'webp') => 'webp',
            default => 'jpg',
        };

        $filename = uniqid('img_').'.'.$extension;
        $path = "group-items/original/$filename";
        $fullPath = storage_path("app/public/$path");

        // Mappa létrehozása, ha nem létezik
        if (! file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $decoded);

        ProcessImage::dispatch($path);

        return $path;
    }
}

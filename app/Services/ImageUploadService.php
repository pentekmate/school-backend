<?php

namespace App\Services;

use App\Jobs\ProcessImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    /**
     * Átmozgatja a temp mappából a végleges helyre és elindítja a feldolgozást.
     */
    public function finalizeTempImage(string $tempPath): string
    {
        // 1. Logoljuk, mit kapunk
        Log::info('Fájl mozgatása indul: '.$tempPath);

        if (str_starts_with($tempPath, 'temp/')) {
            $fileName = basename($tempPath);
            $finalPath = "/".$fileName; 

            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->move($tempPath, $finalPath);
                Log::info('Sikeres mozgatás ide: '.$finalPath);

                // ProcessImage::dispatch($finalPath);

                return $finalPath;
            } else {
                Log::error('A fájl nem létezik a public disken: '.$tempPath);
            }
        }

        return $tempPath;
    }

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

        Storage::disk('public')->put($path, $decoded);
        ProcessImage::dispatch($path);

        return $path;
    }
}

<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public function storeImage(UploadedFile $file, string $name, int $id, string $directory = 'products_images'): string
    {
        $imageName = Str::slug($name, '-') . '-' . $id . '.' . 'png';
        return $file->storeAs($directory, $imageName, 'public');
    }

    public function deleteImage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}

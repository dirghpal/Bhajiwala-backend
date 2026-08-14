<?php

namespace App\Services;

use App\Helpers\CommonHelper;

class UploadService
{
    /**
     * Upload image to temp folder
     */
    public function upload($file): array
    {
        $filename = CommonHelper::uploadImage($file, 'temp');

        return [
            'filename' => $filename,
            'path' => 'temp/' . $filename,
            'url' => CommonHelper::imageUrl($filename, 'temp'),
        ];
    }

    /**
     * Move image from temp to destination folder
     */
    public function move(string $filename, string $folder): bool
    {
        return CommonHelper::moveImage($filename, 'temp', $folder);
    }

    /**
     * Delete image
     */
    public function delete(?string $filename, string $folder): bool
    {
        return CommonHelper::deleteImage($filename, $folder);
    }
}
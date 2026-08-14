<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommonHelper
{
    /**
     * Generate Slug
     */
    public static function slug(string $text): string
    {
        return Str::slug($text);
    }

    /**
     * Generate Unique Filename
     */
    public static function generateFileName($file): string
    {
        return time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Upload Image
     */
    public static function uploadImage($file, string $folder = 'temp'): string
    {
        $filename = self::generateFileName($file);

        $file->storeAs($folder, $filename, 'public');

        return $filename;
    }

    /**
     * Move Image
     */
    public static function moveImage(string $filename, string $from, string $to): bool
    {
        if (!Storage::disk('public')->exists($from.'/'.$filename)) {
            return false;
        }

        return Storage::disk('public')->move(
            $from.'/'.$filename,
            $to.'/'.$filename
        );
    }

    /**
     * Delete Image
     */
    public static function deleteImage(?string $filename, string $folder): bool
    {
        if (!$filename) {
            return false;
        }

        $path = $folder.'/'.$filename;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return true;
        }

        return false;
    }

    /**
     * Image URL
     */
    public static function imageUrl(?string $filename, string $folder): ?string
    {
        if (!$filename) {
            return null;
        }

        return asset('storage/'.$folder.'/'.$filename);
    }
}
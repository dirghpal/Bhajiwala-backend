<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('uploadImage')) {

    function uploadImage($file, $folder = 'temp')
    {
        if (!$file) {
            return null;
        }

        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        $file->storeAs($folder, $fileName, 'public');

        return $fileName;
    }
}

if (!function_exists('moveImage')) {

    function moveImage($fileName, $from, $to)
    {
        if (!$fileName) {
            return null;
        }

        Storage::disk('public')->move(
            $from . '/' . $fileName,
            $to . '/' . $fileName
        );

        return $fileName;
    }
}

if (!function_exists('deleteImage')) {

    function deleteImage($fileName, $folder)
    {
        if (!$fileName) {
            return;
        }

        Storage::disk('public')->delete(
            $folder . '/' . $fileName
        );
    }
}

if (!function_exists('imageUrl')) {

    function imageUrl($fileName, $folder)
    {
        if (!$fileName) {
            return null;
        }

        return asset('storage/' . $folder . '/' . $fileName);
    }
}
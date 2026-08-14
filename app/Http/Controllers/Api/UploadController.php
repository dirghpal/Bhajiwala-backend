<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $image = uploadImage($request->file('image'));

        return response()->json([
            'success' => true,
            'message' => 'Image Uploaded Successfully',
            'data' => [
                'image' => $image,
                'url' => imageUrl($image, 'temp')
            ]
        ]);
    }
}
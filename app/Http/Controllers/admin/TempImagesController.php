<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;
        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension(); // Fixed typo
            $newName = time() . "." . $ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            // Save the image to the temp directory
            $image->move(public_path() . '/temp', $newName);

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'message' => "Image uploaded successfully"
            ]);
        }
    }
}

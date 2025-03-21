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
        if(!empty($image)){
            $ext = $image->getClientOrignalExtension();
            $newName = time()."-".$ext;
            $tempImage = new TempImage();
            
        }
    }
}

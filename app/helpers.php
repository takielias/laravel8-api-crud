<?php

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

function ajaxImageUpload(Request $request, $uploadPath)
{

    $extension = $request->file('image')->getClientOriginalExtension();
    $filename = uniqid() . '_' . time() . '.' . $extension;

    if (!File::exists(public_path($uploadPath . '/' . $request->image_save_path))) {
        File::makeDirectory(public_path($uploadPath . '/'), 0777, true, true);
    }
//        $request->file('file')->move(public_path('images/' . $request->image_save_path), $filename);

//    $img = Image::make($request->file('file'))->resize($width, null, function ($constraint) {
//        $constraint->aspectRatio();
//        $constraint->upsize();
//    });

    $img = Image::make($request->file('image'));


//        if ($img->width() > $img->height()) {
//            $img->rotate(-90);
//        }

    $img->save(public_path($uploadPath . '/' . $filename));

    return $uploadPath . '/' . $filename;
}

function imageUpload($file, $uploadPath): string
{
    $fileName = md5(time() . rand()) . '.' . $file->getClientOriginalExtension();
    $path = public_path($uploadPath);
    if (!File::exists($path)) {
        File::makeDirectory($path, $mode = 0777, true, true);
    }
    $file->move(public_path($uploadPath), $fileName);
    return $uploadPath . '/' . $fileName;
}

function axiosFileUpload(Request $request, $uploadPath)
{
    $imageName = '';
    if ($request->get('image')) {
        $image = $request->get('image');
        $imageName = md5(time() . rand()) . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        \Intervention\Image\Facades\Image::make($request->get('image'))->save(public_path($uploadPath . '/') . $imageName);
    }
    return $uploadPath . '/' . $imageName;
}

function deleteDocument($document): bool
{
    if (File::exists(public_path($document))) {
        File::delete(public_path($document));
        $document->delete();
        return true;
    }
    return false;
}

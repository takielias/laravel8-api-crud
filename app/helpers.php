<?php

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

function axiosFileUpload(Request $request, $uploadPath)
{
    $imageName = '';
    if ($request->get('image')) {
        $image = $request->get('image');
        $imageName = md5(time() . rand()) . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        Image::make($request->get('image'))->save(public_path($uploadPath . '/') . $imageName);
    }
    return $uploadPath . '/' . $imageName;
}

function deleteImage($path): bool
{
    if (File::exists(public_path($path))) {
        File::delete(public_path($path));
        return true;
    }
    return false;
}

function validateBase64Image($data)
{
    try {
        $binary = base64_decode(explode(',', $data)[1]);
        $data = getimagesizefromstring($binary);
    } catch (\Exception $e) {
        return false;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif'];

    if (!$data) {
        return false;
    }

    if (!empty($data[0]) && !empty($data[0]) && !empty($data['mime'])) {
        if (in_array($data['mime'], $allowed)) {
            return true;
        }
    }

    return false;
}

<?php

use Illuminate\Support\Facades\File;

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

function deleteDocument($document): bool
{
    if (File::exists(public_path($document))) {
        File::delete(public_path($document));
        $document->delete();
        return true;
    }
    return false;
}

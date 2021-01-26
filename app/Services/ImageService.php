<?php


namespace App\Services;


class ImageService
{
    public function saveImage($image, $folder) {
        $filenameWithExt = $image->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $fileNameToStore = "$folder/".md5($filename."_".time()).".".$extension;
        $image->storeAs('public', $fileNameToStore);
        return '/storage/' . $fileNameToStore;
    }

    public function deleteImage($imageName) {
        $imageName = str_replace('/storage', 'app/public', $imageName);
        if (unlink(storage_path($imageName))) {
            return true;
        }
        return false;
    }
}

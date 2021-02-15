<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use Image;

class AdvertController extends Controller
{
    protected $mainFolder = 'banners';

    protected function saveBanner($image, $subFolder) {
        $folder = $this->mainFolder . "/" . $subFolder;
        return Image::saveImage($image, $folder);
    }

    protected function deleteBanner($bannerName) {
        return Image::deleteImage($bannerName);
    }
}

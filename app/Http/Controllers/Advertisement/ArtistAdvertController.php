<?php

namespace App\Http\Controllers\Advertisement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ArtistAdvert;

class ArtistAdvertController extends AdvertController
{
    public function getAll($bannerId = null) {
        try {
            $advert = ArtistAdvert::all();
            if ($bannerId != null) $advert = ArtistAdvert::findOrFail($bannerId);
            return response()->json(['artist_adverts' => $advert, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'error'], 400);
        }

    }

    public function save(Request $request) {
        $validator = Validator::make($request->all(), [
            'banner' => 'required|image',
            'artist' => 'required',
            'genre' => 'required',
            'description' => 'string',
            'url' => 'required|string'
        ],
        [
            'banner.required' => 'Баннер не прикреплен',
            'banner.image' => 'Баннер не является изображением',
            'artist.required' => 'Поле "Исполнитель" не заполнено',
            'genre.required' => 'Поле "Жанр" не заполнено',
            'url.required' => 'Ссылка не указана'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        $banner = $request->file('banner');
        $artist = $request->get('artist');
        $genre = $request->get('genre');
        $description = null;
        $url = $request->get('url');

        if ($request->has('description')) $description = $request->get('description');

        try {
            $bannerPath = $this->saveBanner($banner, 'artists_banners');
            ArtistAdvert::create([
                'banner' => $bannerPath,
                'artist' => $artist,
                'genre' => $genre,
                'description' => $description,
                'url' => $url
            ]);
            return response()->json(['message' => 'Баннер успешно сохранен','status' => 'success'], 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function delete($bannerId) {
        try {
            $advert = ArtistAdvert::findOrFail($bannerId)->first();
            $bannerName = $advert->banner;
            $this->deleteBanner($bannerName);
            $advert->delete();
            return response()->json(['message' => 'Баннер успешно удален','status' => 'success'], 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function edit(Request $request, $bannerId) {
        $validator = Validator::make($request->all(), [
            'banner' => 'image'
        ],
        [
            'banner.image' => 'Баннер не является изображением'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        try {
            $advert = ArtistAdvert::findOrFail($bannerId);
            $bannerName = $advert->banner;

            if ($request->has('banner')) {
                $this->deleteBanner($bannerName);
                $newBanner = $request->file('banner');
                $newBannerPath = $this->saveBanner($newBanner, 'artists_banners');
                $advert->banner = $newBannerPath;
                $advert->save();
            }

            $advert->update($request->only('artist', 'genre', 'description', 'url'));
            $advert->save();

            $advert->delete();
            return response()->json(['message' => 'Баннер успешно отредактирован','status' => 'success'], 200);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }
}

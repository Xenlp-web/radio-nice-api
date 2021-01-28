<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stream;
use Illuminate\Support\Facades\Validator;
use Radio;
use Image;

class StreamController extends Controller
{
    public function get($streamId = null) {
        try {
            $streams = Stream::all();
            if ($streamId != null) $streams = Stream::findOrFail($streamId);
            return response()->json(['streams' => $streams, 'status' => 'success']);
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function getStreamUrl(Request $request, $streamId) {
        $user = $request->user('sanctum');

        try {
            $serverId = Stream::findOrFail($streamId)->server_id;
            $streamUrl = Radio::getStreamUrl($serverId, $user);
            if (!$streamUrl) throw new \Exception('Не удалось получить ссылку на стрим');
            return response()->json(['stream_url' => $streamUrl,'status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function getCurrentTrack($streamId) {
        try {
            $serverId = Stream::findOrFail($streamId)->server_id;
            $currentTrack = Radio::getCurrentTrack($serverId);
            if (!$currentTrack) throw new \Exception('Не удалось получить текущий трек');
            return response()->json(['current_track' => $currentTrack,'status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function save(Request $request) {
        $validator = Validator::make($request->all(), [
            'server_id' => 'required|integer|unique:App\Models\Stream',
            'video_stream_link' => 'string|nullable',
            'title' => 'required',
            'genre' => 'required|string',
            'description' => 'nullable',
            'main_image' => 'image|required',
            'thumbnail' => 'image|required'
        ],
        [
            'server_id.required' => 'Не указан id сервера',
            'server_id.integer' => 'id сервера должен быть целым числом',
            'server_id.unique' => 'Трансляция с таким id уже существует',
            'title.required' => 'Название трансляции не указано',
            'genre.required' => 'Жанр не указан',
            'main_image.required' => 'Главное изображение не загружено',
            'main_image.image' => 'Главное изображение не является изображением',
            'thumbnail.required' => 'Иконка не загружена',
            'thumbnail.image' => 'Иконка не является изображением',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        $serverId = $request->get('server_id');
        $title = $request->get('title');
        $genre = $request->get('genre');
        $mainImage = $request->file('main_image');
        $thumbnail = $request->file('thumbnail');
        $videoStreamLink = $request->get('video_stream_link');
        $description = $request->get('description');

        try {
            if (!Radio::checkServerAvailability($serverId)) throw new \Exception('Сервер не доступен');

            $mainImageUrl = Image::saveImage($mainImage, 'streams/main');
            $thumbnailUrl = Image::saveImage($thumbnail, 'streams/thumbnail');

            Stream::create([
                'server_id' => $serverId,
                'video_stream_link' => $videoStreamLink,
                'title' => $title,
                'genre' => $genre,
                'description' => $description,
                'main_image' => $mainImageUrl,
                'thumbnail' => $thumbnailUrl
            ]);

            return response()->json(['message' => 'Трансляция успешно создана','status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function delete(Request $request, $streamId) {
        $user = $request->user('sanctum');
        try {
            $stream = Stream::findOrFail($streamId);
            Image::deleteImage($stream->main_image);
            Image::deleteImage($stream->thumbnail);
            $stream->delete();
            return response()->json(['message' => 'Трансляция успешно удален','status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function edit(Request $request, $streamId) {
        $validator = Validator::make($request->all(), [
            'server_id' => 'integer|unique:App\Models\Stream',
            'video_stream_link' => 'string|nullable',
            'title' => '',
            'genre' => 'string',
            'description' => 'nullable',
            'main_image' => 'image',
            'thumbnail' => 'image'
        ],
        [
            'server_id.integer' => 'id сервера должен быть целым числом',
            'server_id.unique' => 'Трансляция с таким id уже существует',
            'thumbnail.image' => 'Иконка не является изображением',
            'main_image.image' => 'Главное изображение не является изображением'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        try {
            if ($request->has('server_id')) {
                if (!Radio::checkServerAvailability($request->input('server_id'))) throw new \Exception('Сервер трансляции не доступен');
            }

            $stream = Stream::findOrFail($streamId);
            $stream->update($request->only('server_id', 'video_stream_link', 'title', 'genre', 'description'));
            $stream->save();

            if ($request->has('main_image')) {
                $newMainImage = Image::saveImage($request->file('main_image'), 'streams/main');

                if ($stream->main_image != null) Image::deleteImage($stream->main_image);

                $stream->main_image = $newMainImage;
                $stream->save();
            }

            if ($request->has('thumbnail')) {
                $newThumbnail = Image::saveImage($request->file('thumbnail'), 'streams/thumbnail');

                if ($stream->thumbnail != null) Image::deleteImage($stream->thumbnail);

                $stream->thumbnail = $newThumbnail;
                $stream->save();
            }

            return response()->json(['message' => 'Трансляция отредактирована','status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }
}

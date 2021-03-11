<?php

namespace App\Http\Controllers;

use App\Events\PremiumPurchased;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Image;
use Illuminate\Support\Facades\Event;

class UserController extends Controller
{
    public function get($userId = null) {
        try {
            $user = User::all();
            if ($userId != null) $user = User::findOrFail($userId);
            return response()->json(['users' => $user, 'status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'error'], 400);
        }
    }


    public function getCurrent(Request $request) {
        $user = $request->user('sanctum');
        return response()->json(['user' => $user, 'status' => 'success']);
    }


    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:20',
            'surname' => 'string|max:32',
            'email' => 'email',
            'avatar' => 'image'
        ],
        [
            'name.string' => 'Поле Имя не является строкой',
            'surname.string' => 'Поле Фамилия не является строкой',
            'name.max' => 'Поле Имя не должно превышать :max знаков',
            'surname.max' => 'Поле Фамилия не должно превышать :max знаков',
            'email.email' => 'Поле Email не является корректной электронной почтой',
            'avatar.image' => 'Аватар не является изображением'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        $user = $request->user();

        try {
            $user->update($request->only('name', 'surname', 'email'));
            $user->save();

            if ($request->has('avatar')) {
                $newAvatar = Image::saveImage($request->file('avatar'), 'users/avatars');

                if ($user->avatar != null) Image::deleteImage($user->avatar);

                $user->avatar = $newAvatar;
                $user->save();
            }

            return response()->json(['user' => $user,'status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function getSocials(Request $request) {
        try {
            $user = $request->user('sanctum');
            return response()->json(['socials' => $user->only('vkontakte_id', 'facebook_id', 'google_id', 'yandex_id', 'mailru_id'), 'status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage(), 'status' => 'error'], 400);
        }
    }
}

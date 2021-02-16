<?php

namespace App\Http\Controllers\AuthSocial;

use App\Http\Controllers\Controller;
use Socialite;
use App\Models\User;

class VKController extends Controller
{
    public function init() {
        return Socialite::driver('vkontakte')->redirect();
    }

    public function callback() {
        $socialUser = Socialite::driver('vkontakte')->user();
        $id = $socialUser->id;
        $email = $socialUser->email;
        $fullName = explode(" ", $socialUser->name);

        $user = User::where('email', $email)->first();

        if ($user == null) {
            $user = User::create([
                'email' => $email,
                'name' => $fullName[0],
                'surname' => $fullName[1],
                'password' => bcrypt($id . $fullName[0] . $email . 'radio-nice')
            ]);
            $user->sendEmailVerificationNotification();
        }

        $token = $user->createToken('access_token')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token, 'status' => 'success']);
    }
}

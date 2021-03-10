<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Socialite;

class AuthSocial extends Controller
{
    public function init($driver) {
        return Socialite::driver($driver)->redirect();
    }

    public function getToken($driver) {
        return redirect("https://radio-nice.ru/#/auth-social?driver={$driver}&token=" . Socialite::driver($driver)->user()->token);
    }

    public function execute(Request $request, $driver) {
        $validator = Validator::make($request->all(), [
           'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        $socialUser = Socialite::driver($driver)->userFromToken($request->get('token'));
        $id = $socialUser->id;
        $email = $socialUser->email;
        $fullName = explode(" ", $socialUser->name);
        $column = $driver . '_id';
        $user = User::where($column, $id)->first();

        if ($user == null) {
            $user = User::create([
                'email' => $email,
                'name' => $fullName[0],
                'surname' => $fullName[1],
                'password' => bcrypt($id . $fullName[0] . $email . 'radio-nice'),
                $column => $id
            ]);
            $user->sendEmailVerificationNotification();
        }

        $token = $user->createToken('access_token')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token, 'status' => 'success']);
    }

    public function linkAccount(Request $request, $driver) {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        try {
            $user = $request->user('sanctum');
            $socialUser = Socialite::driver($driver)->userFromToken($request->get('token'));
            $column = $driver . '_id';
            $user->$column = $socialUser->id;
            return response()->json(['user' => $user, 'status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }
}

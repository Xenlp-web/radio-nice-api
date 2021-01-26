<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:App\Models\User',
            'password' => 'required|min:8',
            'password_again' => 'required|same:password',
            'name' => 'required|string|max:20',
            'surname' => 'required|string|max:32'
        ],
        [
            'email.required' => 'Введите Email',
            'email.unique' => 'Такой Email уже существует',
            'email.email' => 'Введенный Email не соответствует электронной почте',
            'password.required' => 'Введите пароль',
            'password.min' => 'Минимальное количество символов для пароля - :min',
            'password_again.required' => 'Пароли не совпадают',
            'password_again.same' => 'Пароли не совпадают',
            'name.required' => 'Введите имя',
            'name.max' => 'Максимальное количество символов для имени - :max',
            'surname.required' => 'Введите фамилию',
            'surname.max' => 'Максимальное количество символов для фамилии - :max'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        try {
            $user = User::create([
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'password' => bcrypt($request->input('password'))
            ]);
            $token = $user->createToken('access_token')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token, 'status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ],
        [
            'email.required' => 'Введите Email',
            'email.email' => 'Введенный Email не соответствует электронной почте',
            'password.required' => 'Введите пароль'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        try {
            Auth::attempt($request->only('email', 'password'));
            $user = Auth::user();
            $token = $user->createToken('access_token')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token, 'status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage(), 'status' => 'error'], 400);
        }
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Вы вышли из аккаунта', 'status' => 'success'],200);
    }
}

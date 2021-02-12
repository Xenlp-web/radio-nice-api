<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

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
            $user->sendEmailVerificationNotification();
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
            if (!Auth::attempt($request->only('email', 'password'))) throw new \Exception('Пароль или email введены неверно');
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

    public function forgotPassword(Request $request){
        $email = $request->only('email');
        $password = $request->get('password');

        $validator = Validator::make($request->only('email', 'password', 'password_confirmation'), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $user = User::where('email', $email['email'])->firstOrFail();
            $user->tmp_password = bcrypt($password);
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 'error'], 400);
        }

        $response = Password::sendResetLink($email);
        $message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : 'Mail error';
        return response()->json($message);
    }

    public function passwordReset(Request $request){
        $input = $request->only('email', 'token');
        $validator = Validator::make($input, [
            'token' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::where('email', $input['email'])->firstOrFail();
        $input['password'] = $user->tmp_password;

        $response = Password::reset($input, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        $user->tmp_password = null;
        $user->save();

        $message = $response == Password::PASSWORD_RESET ? 'Password reset successfully' : 'Password reset error';
        return redirect('/');
    }
}

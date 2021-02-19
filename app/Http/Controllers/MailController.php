<?php

namespace App\Http\Controllers;

use App\Mail\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendFeedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required',
            'message' => 'required'
        ],
            [
                'email.required' => 'Поле email не заполнено',
                'email.email' => 'Email указан некоректно',
                'name.required' => 'Имя не указано',
                'message.required' => 'Сообщение не заполнено'
            ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        $email = $request->get('email');
        $name = $request->get('name');
        $message = $request->get('message');

        try {
            Mail::to('info@radio-nice.ru')->send(new Feedback($name, $email, $message));
            return response()->json(['message' => 'Письмо отправлено','status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'str' => $e->getTraceAsString(), 'status' => 'error'], 400);
        }
    }
}

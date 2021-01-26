<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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


}

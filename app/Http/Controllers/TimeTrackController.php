<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeTracker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TimeTrackController extends Controller
{
    public function update (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seconds' => 'integer'
        ],
        [
            'seconds.integer' => 'Секунды должны быть представлены целым числом'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()->all(), 'status' => 'error'], 400);
        }

        $user = $request->user('sanctum');
        $ip = $request->getClientIp();

        try {
            $tracker = TimeTracker::firstOrCreate(
                            ['ip' => $ip],
                            ['user_id' => $user != NULL ? $user->id : NULL]
                        );

            if ($request->has('seconds')) {
                $tracker->passed_seconds = $request->get('seconds');
                $tracker->save();
            }

            $isBlocked = $tracker->passed_seconds >= 3600;
            if ($user != NULL && $user->premium) $isBlocked = FALSE;
            return response()->json(['blocked' => $isBlocked, 'passed_seconds' => $tracker->passed_seconds, 'status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage(), 'status' => 'error'], 400);
        }
    }
}

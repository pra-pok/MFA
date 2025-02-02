<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserActionController extends Controller
{
    public function logUserAction(Request $request) {
        $teamId = $request->user()->team_id;
        $userId = $request->user()->id;
        $logger = teamUserLogger($teamId, $userId);
        $logger->info('User performed an action.', [
            'action' => 'Logged In',
            'timestamp' => now(),
        ]);
        return response()->json(['message' => 'Log saved successfully.']);
    }
}

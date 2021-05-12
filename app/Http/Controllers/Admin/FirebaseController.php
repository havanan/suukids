<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FirebaseController extends Controller {
    public function setDeviceToken(Request $request) {
        $token = $request->get('token');
        if (empty($token)) {
            return;
        }
        try {
            $user = getCurrentUser();
            FcmToken::query()->updateOrCreate(['token' => $token], [
                'user_id' => $user->id,
                'token' => $token
            ]);
        } catch(\Exception $e) {

        }
    }
}
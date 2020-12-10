<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoginController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public static function byOAuthToken(Request $request)
    {
        $userData = Socialite::driver($request->get('provider'))->userFromToken($request->get('token'));
        try {
            $user = static::where('provider', Str::lower($request->get('provider')))->where('provider_id', $userData->getId())->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = static::create([
                'name' => $userData->getName(),
                'email' => $userData->getEmail(),
                'provider' => $request->get('provider'),
                'provider_id' => $userData->getId(),
                'password' => Hash::make(Str::random(16)),
                'avatar' => $userData->getAvatar()
            ]);
        }
        Auth::onceUsingId($user->id);
        return $user;
    }
}

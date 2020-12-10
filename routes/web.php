<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
});

Route::get('/auth/callback', function (Request $request) {
    $userData = Socialite::driver('github')->user();
    dd($userData);
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
});

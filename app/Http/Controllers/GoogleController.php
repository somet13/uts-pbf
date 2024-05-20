<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                Auth::login($user);
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt('123456dummy')
                ]);
                Auth::login($user);
            }

            $token = auth()->guard('api')->login($user);

            return response()->json([
                "status" => true,
                "message" => "Login with Google success",
                "token" => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Something went wrong",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $accessToken = $request->bearerToken();
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

            $userData = $response->json();

            if (isset($userData['error'])) {
                return response()->json([
                    'error' => $userData['error'],
                    'message' => 'token tidak valid harap get new Access Token terlebihi dahulu'
                ], 500);
            }

            $name = $userData['name'];
            $email = $userData['email'];

            // register dan tampilkan token
            $user = User::where('email', $email)->first();
            if ($user) {
                $token = auth()->guard('api')->login($user);

                return response()->json([
                    "status" => true,
                    "message" => "Login success",
                    "token" => $token
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt('123456dummy')
                ]);
                $token = auth()->guard('api')->login($user);

                return response()->json([
                    "status" => true,
                    "message" => "Login with Google success",
                    "token" => $token
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'       => 'required|string',
    //         'password'    => 'required|string',
    //         'apk_version' => 'required|string', // Assuming version is a string like '1.0.0'
    //     ], [
    //         'email.required'       => 'Please enter your email or username.',
    //         'password.required'    => 'Please enter your password.',
    //         'apk_version.required' => 'App version is required.',
    //     ]);

    //     // Step 2: Check if the app version is up-to-date
    //     $minApkVersion = '1.5.0'; // You can store this in a config or database

    //     if (version_compare($request->input('apk_version'), $minApkVersion, '<')) {
    //         return response()->json([
    //             'status'  => 426, // 426 - Upgrade Required
    //             'msg'     => "Please update your app to the latest version.",
    //             'min_version' => $minApkVersion,
    //         ], 426);
    //     }

    //     // Step 3: Determine if the user logged in with an email or username
    //     $loginType = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    //     $credentials = [
    //         $loginType => $request->input('email'),
    //         'password' => $request->input('password'),
    //     ];

    //     if (!Auth::attempt($credentials)) {
    //         throw ValidationException::withMessages([
    //             'email' => ['The provided credentials are incorrect.'],
    //         ]);
    //     }

    //     $user = Auth::user();

    //     $token = bin2hex(openssl_random_pseudo_bytes(30)); // Generate a random token
    //     $user->api_token = $token;
    //     $user->save();

    //     return response()->json([
    //         'status'        => 200,
    //         'msg'           => "You have login successfully",
    //         'token_type'    => 'Bearer',
    //         'access_token'  => $token,
    //         'user_id'       => $user->id,
    //     ]);
    // }
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

    
        $loginType = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        $token = bin2hex(openssl_random_pseudo_bytes(30)); // Generate a random token
        $user->api_token = $token;
        $user->save();

        return response()->json([
            'status'        => 200,
            'msg'           => "You have login successfully",
            'token_type'    => 'Bearer',
            'access_token'  => $token,
            'user_id'       => $user->id,
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the user's API token
        $user = Auth::user();
        $user->api_token = null;
        $user->save();

        return response()->json(['message' => 'Logged out successfully']);
    }
}

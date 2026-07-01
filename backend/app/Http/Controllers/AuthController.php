<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. Standard Registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('API_Token')->plainTextToken;

        return response()->json([
            'message' => 'បង្កើតគណនីជោគជ័យ!',
            'token' => $token,
            'role' => $user->role
        ], 201);
    }

    // 2. Standard Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email ឬ Password មិនត្រឹមត្រូវទេ'], 401);
        }

        // Generate a Sanctum token
        $token = $user->createToken('API_Token')->plainTextToken;

        return response()->json([
            'message' => 'ចូលគណនីជោគជ័យ!',
            'token' => $token,
            'role' => $user->role
        ]);
    }

    // 3. Google OAuth Redirect
    public function redirectToGoogle()
    {
        // stateless() is crucial here since APIs don't use session state
        return Socialite::driver('google')->stateless()->redirect();
    }

    // 4. Google OAuth Callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find existing user or create a new one
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'user'
                ]
            );

            // Generate a Sanctum token
            $token = $user->createToken('API_Token')->plainTextToken;

            // Redirect back to the Vue frontend, passing the token in the URL
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect($frontendUrl . '/dashboard?token=' . $token);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Google Authentication failed: ' . $e->getMessage()], 400);
        }
    }
}

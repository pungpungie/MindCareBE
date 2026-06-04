<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    /**
     * Register user baru
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'umur' => 'nullable|integer|min:10|max:100',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'username.regex' => 'Username hanya boleh huruf, angka, dan underscore'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'umur' => $request->umur,
            'gender' => $request->gender,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 'Registrasi berhasil', 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->unauthorizedResponse('Email atau password salah');
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 'Login berhasil');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'Logout berhasil');
    }

    /**
     * Get profile user
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Hitung statistik
        $stats = [
            'total_mood_check' => $user->moodCheckins()->count(),
            'total_jurnal' => $user->dailyJournals()->count(),
            'total_chat' => $user->chatbotMessages()->count(),
            'last_mood' => $user->moodCheckins()->latest()->first(),
            'mood_trend' => $this->getMoodTrend($user)
        ];

        return $this->successResponse([
            'user' => $user,
            'stats' => $stats
        ], 'Data profil');
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:50|unique:users,username,' . $request->user()->id . '|regex:/^[a-zA-Z0-9_]+$/',
            'umur' => 'nullable|integer|min:10|max:100',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $request->user()->update($request->only(['name', 'username', 'umur', 'gender']));

        return $this->successResponse($request->user(), 'Profil berhasil diupdate');
    }

    /**
     * Hitung tren mood
     */
    private function getMoodTrend($user)
    {
        $lastTwo = $user->moodCheckins()->latest()->take(2)->get();
        
        if ($lastTwo->count() < 2) {
            return 'Data belum cukup';
        }

        $current = $lastTwo[0]->mood_score;
        $previous = $lastTwo[1]->mood_score;

        if ($current < $previous) return 'Membaik 📉';
        if ($current > $previous) return 'Memburuk 📈';
        return 'Stabil ➡️';
    }

    /**
 * Hapus akun user yang sedang login
 */
    public function deleteAccount(Request $request)
{
    $user = $request->user();

    // Hapus semua token (logout dari semua perangkat)
    $user->tokens()->delete();

    // Hapus user (data terkait akan terhapus otomatis karena cascade)
    $user->delete();

    return $this->successResponse(null, 'Akun berhasil dihapus');
}
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        if ($user = User::firstWhere('no_hp', $request->no_hp)) {
            return response()->json($user);
        }

        return response()->json([
            'message' => 'No HP tidak terdaftar',
        ], 401);
    }
    public function LoginUser(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string|',
        ]);

        $credentials = request(['no_hp']);

        $no_hp = $credentials['no_hp'];

        $user = User::where('no_hp', $no_hp)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No HP tidak terdaftar'
            ], 401);
        }


        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if (!$tokenResult) {
            return response()->json([
                'message' => 'Gagal membuat token'
            ], 500);
        }

        return response()->json([
            'message' => 'berhasil login',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
}
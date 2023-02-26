<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        if ($user = User::firstWhere('no_hp', $request->no_hp)) {
            return response()->json($user);
        }

        return response()->json([
            'message' => 'No HP tidak ditemukan',
        ], 401);
    }
}

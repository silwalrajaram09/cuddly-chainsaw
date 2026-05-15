<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
           
        ]);

        $member = User::where('email', $request->email)
                        ->where('type', 'admin')
                        ->first();

        if (! $member) {
            return response()->json([
                'message' => 'No admin account found with this email.',
            ], 401);
        }

        if (! Hash::check($request->password, $member->password)) {
            return response()->json([
                'message' => 'Incorrect password.',
            ], 401);
        }

        // Delete old tokens so only one session at a time
        $member->tokens()->delete();

        $token = $member->createToken('admin-token')->plainTextToken;
        

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'       => $member->user_id,
                'name'     => $member->full_name,
                'email'    => $member->email,
                'type'     => $member->type,
               
            ],
        ], 200);
    }
    public function logout(Request $request)
    {
        // Delete only the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }

   
    
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function login(Request $request){
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('my-api')->accessToken;
            return response()->json([
                'status' => 'true',
                'message' => 'Login success',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $token
                ]
            ], 200);
        }else {
            return response()->json([
                'status' => 'false',
                'message' => 'otentikasi gagal'
            ]);
        }
    }

    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => true,
            'message' => 'Daftar semua user',
            'data' => $users
        ], 200);
    }
    
    public function show($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'Data user ditemukan',
        'data' => $user
    ], 200);
}
}
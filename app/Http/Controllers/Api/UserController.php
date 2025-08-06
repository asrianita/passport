<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $user = User::create($data);

        
        return response()->json([
            'status' => 'true',
            'message' => 'Registrasi berhasil',
            'data' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }}
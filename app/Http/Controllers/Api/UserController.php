<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

     public function index(Request $request)
{
    // Ambil query search dari request (jika ada)
    $search = $request->input('search');

    // Ambil nomor halaman dari query string, default = 1
    $page = $request->input('page', 1);

    // Query dasar
    $query = User::query();

    // Kalau ada parameter search, filter berdasarkan name atau email
    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Ambil data user 10 per halaman
    $users = $query->paginate(10, ['*'], 'page', $page);

    // Kalau tidak ada hasil sama sekali
    if ($users->total() === 0) {
        return response()->json([
            'status' => false,
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    // Kalau halaman di-request lebih besar dari total halaman tersedia
    if ($page > $users->lastPage()) {
        return response()->json([
            'status' => false,
            'message' => 'Halaman tidak ditemukan'
        ], 404);
    }

    // Format response custom
    return response()->json([
        'status' => true,
        'message' => 'Daftar user',
        'data' => [
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem()
            ]
        ]
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
public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil dibuat',
            'data' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User berhasil dihapus'
        ], 200);
    }
}
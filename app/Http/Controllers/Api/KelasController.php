<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Kelas;
use App\Models\User;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);

        $kelas = Kelas::with('user')->paginate(10, ['*'], 'page', $page);

        if ($kelas->total() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        if ($page > $kelas->lastPage()) {
            return response()->json([
                'status' => false,
                'message' => 'Halaman tidak ditemukan'
            ], 404);
        }

        // Format sesuai contoh
        $data = $kelas->map(function($k) {
            return [
                'id' => $k->user->id,
                'nama' => $k->user->name,
                'email' => $k->user->email,
                'kelas' => [
                    'id' => $k->id,
                    'nama_kelas' => $k->nama_kelas,
                    'deskripsi' => $k->deskripsi
                ]
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Daftar kelas',
            'data' => [
                'data' => $data,
                'pagination' => [
                    'total' => $kelas->total(),
                    'per_page' => $kelas->perPage(),
                    'current_page' => $kelas->currentPage(),
                    'last_page' => $kelas->lastPage(),
                    'from' => $kelas->firstItem(),
                    'to' => $kelas->lastItem()
                ]
            ]
        ], 200);
    }

    public function show($id)
    {
        $kelas = Kelas::with('user')->find($id);

        if (!$kelas) {
            return response()->json([
                'status' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail kelas',
            'data' => [
                'id' => $kelas->user->id,
                'nama' => $kelas->user->name,
                'email' => $kelas->user->email,
                'kelas' => [
                    'id' => $kelas->id,
                    'nama_kelas' => $kelas->nama_kelas,
                    'deskripsi' => $kelas->deskripsi
                ]
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:kelas,user_id',
            'nama_kelas' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kelas = Kelas::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Kelas berhasil dibuat',
            'data' => $kelas
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json([
                'status' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'sometimes|required|string|max:100',
            'deskripsi' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kelas->update($request->only(['nama_kelas', 'deskripsi']));

        return response()->json([
            'status' => true,
            'message' => 'Kelas berhasil diperbarui',
            'data' => $kelas
        ], 200);
    }

    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json([
                'status' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        $kelas->delete();

        return response()->json([
            'status' => true,
            'message' => 'Kelas berhasil dihapus'
        ], 200);
    }
}
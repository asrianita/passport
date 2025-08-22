<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;
use App\Models\Kelas;

class KelasApiTest extends TestCase
{
    use DatabaseMigrations;

    public function test_bisa_mendapatkan_daftar_kelas()
    {
        $user = User::factory()->create();
        Kelas::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->getJson('/api/kelas');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'nama_kelas',
                                 'deskripsi'
                             ]
                         ],
                         'pagination' => [
                             'total',
                             'per_page',
                             'current_page',
                             'last_page',
                             'from',
                             'to'
                         ]
                     ]
                 ]);
    }

    public function test_bisa_menambahkan_kelas()
    {
        $user = User::factory()->create();

        $data = [
            'nama_kelas' => 'Kelas Laravel',
            'deskripsi' => 'Belajar API Laravel',
            'user_id' => $user->id
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/kelas', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'nama_kelas' => 'Kelas Laravel',
                     'deskripsi' => 'Belajar API Laravel'
                 ]);

        $this->assertDatabaseHas('kelas', [
            'user_id' => $user->id,
            'nama_kelas' => 'Kelas Laravel'
        ]);
    }

    public function test_bisa_melihat_detail_kelas()
    {
        $user = User::factory()->create();
        $kelas = Kelas::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->getJson("/api/kelas/{$kelas->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'id',
                         'nama_kelas',
                         'deskripsi'
                     ]
                 ]);
    }

    public function test_bisa_update_kelas()
    {
        $user = User::factory()->create();
        $kelas = Kelas::factory()->create(['user_id' => $user->id]);

        $update = [
            'nama_kelas' => 'Kelas Updated',
            'deskripsi' => 'Deskripsi baru'
        ];

        $response = $this->actingAs($user, 'api')->putJson("/api/kelas/{$kelas->id}", $update);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'nama_kelas' => 'Kelas Updated',
                     'deskripsi' => 'Deskripsi baru'
                 ]);

        $this->assertDatabaseHas('kelas', array_merge($update, ['id' => $kelas->id]));
    }

    public function test_bisa_menghapus_kelas()
    {
        $user = User::factory()->create();
        $kelas = Kelas::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->deleteJson("/api/kelas/{$kelas->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Kelas berhasil dihapus'
                 ]);

        $this->assertDatabaseMissing('kelas', [
            'id' => $kelas->id
        ]);
    }
}

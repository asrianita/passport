<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;

class UserApiTest extends TestCase
{
    use DatabaseMigrations;

    public function test_bisa_mendapatkan_daftar_user()
    {
        $authUser = User::factory()->create();
        User::factory()->count(3)->create();

        $response = $this->actingAs($authUser, 'api')->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
                         ],
                         'pagination' => [
                             'total', 'per_page', 'current_page', 'last_page', 'from', 'to'
                         ]
                     ]
                 ]);
    }

    public function test_bisa_menambahkan_user()
    {
        $authUser = User::factory()->create();

        $data = [
            'name' => 'User Baru',
            'email' => 'userbaru@example.com',
            'password' => 'password123'
        ];

        $response = $this->actingAs($authUser, 'api')->postJson('/api/user', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'User Baru',
                     'email' => 'userbaru@example.com'
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => 'User Baru',
            'email' => 'userbaru@example.com'
        ]);
    }

    public function test_bisa_melihat_detail_user()
    {
        $authUser = User::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($authUser, 'api')->getJson("/api/user/{$user->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => ['id', 'name', 'email', 'created_at', 'updated_at']
                 ]);
    }

    public function test_bisa_update_user()
    {
        $authUser = User::factory()->create();
        $user = User::factory()->create();

        $updateData = [
            'name' => 'User Update',
            'email' => 'userupdate@example.com',
            'password' => 'newpassword'
        ];

        $response = $this->actingAs($authUser, 'api')->putJson("/api/user/{$user->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'User Update',
                     'email' => 'userupdate@example.com'
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => 'User Update',
            'email' => 'userupdate@example.com'
        ]);
    }

    public function test_bisa_menghapus_user()
    {
        $authUser = User::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($authUser, 'api')->deleteJson("/api/user/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'User berhasil dihapus'
                 ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
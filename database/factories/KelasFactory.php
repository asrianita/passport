<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kelas;
use App\Models\User;

class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition()
    {
        return [
            'nama_kelas' => $this->faker->sentence(2),
            'deskripsi' => $this->faker->paragraph(),
            'user_id' => User::factory(), // otomatis buat user
        ];
    }
}
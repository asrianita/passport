<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'user_id',
        'nama_kelas',
        'deskripsi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
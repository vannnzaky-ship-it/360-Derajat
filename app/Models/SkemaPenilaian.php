<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkemaPenilaian extends Model
{
    use HasFactory;

    protected $table = 'skema_penilaian';
    
    protected $guarded = ['id'];

    // Ini PENTING: Agar JSON di database otomatis jadi Array di PHP
    protected $casts = [
        'level_target' => 'array', 
    ];

    public function siklus()
    {
        return $this->belongsTo(Siklus::class);
    }
}
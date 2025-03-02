<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;
    protected $fillable = ['conversation'];
    protected $casts = [
        'conversation' => 'array', // Automatically casts JSON to array
    ];
}

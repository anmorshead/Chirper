<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
//pivot table between user and chirp basically (many to many)
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chirp_id',
        'media_path'
    ];

    public function user()
    {
        //one to many relationship (access user)
        return $this->belongsTo(User::class);
    }

    public function chirp()
    {
        //one to many relationship (access chirp)
        return $this->belongsTo(Chirp::class);
    }
}

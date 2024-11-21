<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'chirp_id'
    ];

    /**
     * Get the chirp associated with the like.
     */
    public function chirp()
    {
        return $this->belongsTo(Chirp::class);
    }

    /**
     * Get the user who liked the chirp.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

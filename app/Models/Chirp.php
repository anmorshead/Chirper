<?php

namespace App\Models;

use App\Events\ChirpCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chirp extends Model
{
    public $fillable = [ 'message' ];

    protected $dispatchesEvents = [
        'created' => ChirpCreated::class,
    ];

    public function user(): BelongsTo //foreign key
    {
        return $this->belongsTo('App\Models\User');
    }

    //Update the Chirp model:
    //Add a likes() method to define a one-to-many relationship with the Like model.

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}

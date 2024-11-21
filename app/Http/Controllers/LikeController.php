<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Chirp $chirp)
    {
        // Check if the user has already liked the chirp
        if ($chirp->likes()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Already liked'], 200);
        }

        // Create a new like record
        $chirp->likes()->create([
            'user_id' => $request->user()->id,
        ]);
        return redirect(route('chirps.index'));
    }

    public function destroy(Request $request, Chirp $chirp)
    {
        // Find and delete the like record
        $like = $chirp->likes()->where('user_id', $request->user()->id)->first();

        if ($like) {
            $like->delete();
        }
        return redirect(route('chirps.index'));
    }
}

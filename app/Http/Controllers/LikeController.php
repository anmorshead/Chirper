<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Chirp $chirp)
    {
        // check if user has already liked the chirp
//        if ($chirp->likes()->where('user_id', $request->user()->id)->exists()) {
//            return response()->json(['message' => 'Already liked'], 200);
//        }

        // create new like record
        $chirp->likes()->create([
            'user_id' => $request->user()->id,
        ]);
        //return json response with ajax
        return response()->json(['likeCount' => $chirp->likes()->count()]);
    }

    public function destroy(Request $request, Chirp $chirp)
    {
        // find and delete like record
        $like = $chirp->likes()->where('user_id', $request->user()->id)->first();

        if ($like) {
            $like->delete();
        }
//        return json response with ajax
        return response()->json(['likeCount' => $chirp->likes()->count()]);
    }
}

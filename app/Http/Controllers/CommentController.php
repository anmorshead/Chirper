<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Chirp $chirp)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $chirp->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return back();
    }
}

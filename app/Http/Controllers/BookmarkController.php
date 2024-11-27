<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;
use App\Models\Bookmark;

class BookmarkController extends Controller
{
    public function store(Request $request, Chirp $chirp)
    {
        $chirp->bookmarks()->create([
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Bookmarked successfully']);
    }

    public function destroy(Request $request, Chirp $chirp)
    {
        $chirp->bookmarks()->where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Bookmark removed']);
    }

   public function index()
   {
       //fetch the chirps that logged-in user has bookmarked if any
       $bookmarkedChirps = auth()->user()->bookmarks ? auth()->user()->bookmarks->map->chirp : collect();


       return view('bookmarks.index', compact('bookmarkedChirps'));
   }


}

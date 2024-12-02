<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        //uses Chirp model
        return view('chirps.index', [
            'chirps' => Chirp::with('user')->latest()->get(), //join
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) :RedirectResponse
    {
        //create new chirp, save to database
        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
        ]);

        // Handle file upload
        $mediaPath="";
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('chirps', 'public'); //store in the 'chirps' directory on the public disk
        }

        $request->user()->chirps()->create([
            'message' => $validated['message'],
            'media_path' => $mediaPath,
        ]);

        return redirect(route('chirps.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        return view('chirps.show', ['chirp' => $chirp]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp): View
    {
        Gate::authorize('update', $chirp);

        return view('chirps.edit', [
            'chirp' => $chirp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        Gate::authorize('update', $chirp);

        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240', // Allow image/video updates
        ]);

        // Handle file upload if new media is provided
        if ($request->hasFile('media')) {
            // Delete the old file if it exists
            if ($chirp->media_path) {
                Storage::disk('public')->delete($chirp->media_path);
            }

            $chirp->media_path = $request->file('media')->store('chirps', 'public');
        }

        $chirp->update([
            'message' => $validated['message'],
            'media_path' => $chirp->media_path,
        ]);

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        Gate::authorize('delete', $chirp);

        // Delete the associated media file if it exists
        if ($chirp->media_path) {
            Storage::disk('public')->delete($chirp->media_path);
        }

        $chirp->delete();

        return redirect(route('chirps.index'));
    }
}

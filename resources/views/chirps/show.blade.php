<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
      {{$chirp->message}}
        {{-- Display media if present --}}
        @if ($chirp->media_path)
            <div class="mt-4">
                @if (Str::contains($chirp->media_path, ['.jpg', '.jpeg', '.png', '.gif']))
                    <img src="{{ asset('storage/' . $chirp->media_path) }}" alt="Media" class="w-full h-auto rounded-lg">
                @elseif (Str::contains($chirp->media_path, ['.mp4', '.mov', '.avi']))
                    <video controls class="w-full rounded-lg">
                        <source src="{{ asset('storage/' . $chirp->media_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>

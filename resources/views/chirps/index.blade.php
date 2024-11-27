<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('chirps.store') }}">
            @csrf
            <textarea
                name="message"
                placeholder="{{ __('What\'s on your mind?') }}"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            >{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <x-primary-button class="mt-4">{{ __('Chirp') }}</x-primary-button>
        </form>

        <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
            @foreach ($chirps as $chirp)
                <div class="p-6 flex space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-gray-800">{{ $chirp->user->name }}</span>
                                <small class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->format('j M Y, g:i a') }}</small>
                                @unless ($chirp->created_at->eq($chirp->updated_at))
                                    <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                                @endunless
                            </div>
                            {{--bookmark button--}}
                            <div>
                                <button
                                    class="bookmark-button"
                                    data-chirp-id="{{ $chirp->id }}"
                                    data-bookmarked="{{ $chirp->bookmarks->contains('user_id', auth()->id()) ? 'true' : 'false' }}">
                                    <img
                                        src="{{ $chirp->bookmarks->contains('user_id', auth()->id()) ? asset('bookmark-filled.png') : asset('bookmark.png') }}"
                                        alt="Bookmark"
                                        class="h-6 w-6">
                                </button>
                            </div>
                            @if ($chirp->user->is(auth()->user()))
                                <x-dropdown>
                                    <x-slot name="trigger">
                                        <button>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('chirps.edit', $chirp)">
                                            {{ __('Edit') }}
                                        </x-dropdown-link>
                                        <form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
                                            @csrf
                                            @method('delete')
                                            <x-dropdown-link :href="route('chirps.destroy', $chirp)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                {{ __('Delete') }}
                                            </x-dropdown-link>
                                        </form>
                                        <x-dropdown-link :href="route('chirps.show', $chirp)">
                                            {{ __('Details') }}
                                        </x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            @endif
                        </div>
                        <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>

                        {{-- Comments Section --}}
                        <div class="mt-4">
                            <h3 class="text-sm font-bold">Comments:</h3>
{{--                            loops through all comments in each chirp--}}
                            @foreach ($chirp->comments as $comment)
                                <div class="mt-2">
                                    <span class="font-semibold">{{ $comment->user->name }}:</span>
                                    <span>{{ $comment->content }}</span>
                                    {{--display date--}}
                                    <small class="text-gray-600">({{ $comment->created_at->diffForHumans() }})</small>
                                </div>
                            @endforeach
                        </div>

                        {{-- Add a Comment --}}
                        <form method="POST" action="{{ url('/chirps/' . $chirp->id . '/comments') }}" class="mt-4">
                            @csrf
                            <textarea
                                name="content"
                                placeholder="Write a comment..."
                                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                required
                            ></textarea>
                            <x-primary-button class="mt-2">{{ __('Comment') }}</x-primary-button>
                        </form>

                        {{--Likes--}}
                        <div id="chirp-{{ $chirp->id }}" class="chirp">
                            <div>
                                <button
                                    class="like-button"
                                    data-chirp-id="{{ $chirp->id }}"
                                    data-liked="{{ $chirp->likes->contains('user_id', auth()->id()) ? 'true' : 'false' }}">
                                    <img
                                        src="{{ $chirp->likes->contains('user_id', auth()->id()) ? asset('unlike.png') : asset('like.png') }}"
                                        alt="Like/Unlike"
                                        class="h-6 w-6">
                                </button>
                                <span id="like-count-{{ $chirp->id }}">{{ $chirp->likes->count() }} Likes</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Script for AJAX --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const likeButtons = document.querySelectorAll('.like-button');

            // loops through each button and attaches click listener
            likeButtons.forEach(button => {
                button.addEventListener('click', async () => {
                    //extracts data
                    const chirpId = button.getAttribute('data-chirp-id');
                    const isLiked = button.getAttribute('data-liked') === 'true';

                    //define endpoint dynamically
                    const url = `/chirps/${chirpId}/like`; //like endpoint
                    const method = isLiked ? 'DELETE' : 'POST'; //delete to unlike

                    try {
                        // Send AJAX request
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });
                        //check response, extract data
                        if (response.ok) {
                            const data = await response.json();

                            // Update the like count and button using response data
                            const likeCount = document.querySelector(`#like-count-${chirpId}`);
                            likeCount.textContent = `${data.likeCount} Likes`;

                            const img = button.querySelector('img');
                            if (isLiked) {
                                img.src = '{{ asset("like.png") }}';
                                button.setAttribute('data-liked', 'false');
                            } else {
                                img.src = '{{ asset("unlike.png") }}';
                                button.setAttribute('data-liked', 'true');
                            }
                        } else {
                            console.error('Error toggling like:', response.statusText);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            });
        });
        //bookmarks
        document.addEventListener('DOMContentLoaded', () => {
            const bookmarkButtons = document.querySelectorAll('.bookmark-button');

            bookmarkButtons.forEach(button => {
                button.addEventListener('click', async () => {
                    const chirpId = button.getAttribute('data-chirp-id');
                    const isBookmarked = button.getAttribute('data-bookmarked') === 'true';

                    const url = `/chirps/${chirpId}/bookmark`; // Bookmark endpoint
                    const method = isBookmarked ? 'DELETE' : 'POST'; // DELETE to remove bookmark

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });

                        if (response.ok) {
                            const img = button.querySelector('img');
                            if (isBookmarked) {
                                img.src = '{{ asset("bookmark.png") }}';
                                button.setAttribute('data-bookmarked', 'false');
                            } else {
                                img.src = '{{ asset("bookmark-filled.png") }}';
                                button.setAttribute('data-bookmarked', 'true');
                            }
                        } else {
                            console.error('Error toggling bookmark:', response.statusText);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            });
        });

    </script>
</x-app-layout>

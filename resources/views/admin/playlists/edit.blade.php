@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <div class="mb-4">
            <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">{{ __('Edit Playlist') }}: {{ $playlist->name }}</h1>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <form action="{{ route('admin.playlists.update', $playlist) }}" method="POST" enctype="multipart/form-data"
                  x-data="playlistForm({{
                    json_encode([
                        'vinyls' => $playlist->tracks->map(function($track) {
                            return [
                                'vinyl_master_id' => $track->vinyl_master_id,
                                'vinyl_sec_id' => $track->trackable_id,
                                'title' => $track->vinylMaster->title . ' - ' . $track->vinylMaster->artists->pluck('name')->implode(', ')
                            ];
                        }),
                        'currentImage' => $playlist->image_url
                    ])
                }})"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $playlist->name) }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Slug') }}</label>
                            <input type="text" id="slug" name="slug" value="{{ old('slug', $playlist->slug) }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div>
                            <label for="bio" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Bio') }}</label>
                            <textarea id="bio" name="bio" rows="4"
                                      class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500">{{ old('bio', $playlist->bio) }}</textarea>
                            @error('bio')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Social Links -->
                        <div class="space-y-4">
                            <div>
                                <label for="instagram_url" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Instagram URL') }}</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-lg">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.897 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.897-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                        </svg>
                                    </span>
                                    <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $playlist->instagram_url) }}"
                                           class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-primary-500 focus:border-primary-500 block flex-1 min-w-0 w-full text-sm p-2.5">
                                </div>
                            </div>

                            <div>
                                <label for="youtube_url" class="block mb-2 text-sm font-medium text-gray-900">{{ __('YouTube URL') }}</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-lg">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                    </span>
                                    <input type="url" id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $playlist->youtube_url) }}"
                                           class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-primary-500 focus:border-primary-500 block flex-1 min-w-0 w-full text-sm p-2.5">
                                </div>
                            </div>

                            <div>
                                <label for="facebook_url" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Facebook URL') }}</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-lg">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </span>
                                    <input type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $playlist->facebook_url) }}"
                                           class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-primary-500 focus:border-primary-500 block flex-1 min-w-0 w-full text-sm p-2.5">
                                </div>
                            </div>

                            <div>
                                <label for="soundcloud_url" class="block mb-2 text-sm font-medium text-gray-900">{{ __('SoundCloud URL') }}</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-lg">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M1.175 12.225c-.051 0-.094.046-.101.1l-.233 2.154.233 2.105c.007.058.05.098.101.098.05 0 .09-.04.099-.098l.255-2.105-.27-2.154c-.009-.06-.052-.1-.102-.152-.1m-.899-1.463c-.074 0-.144.068-.144.15l-.164 3.67.184 3.61c.007.082.07.15.144.15.078 0 .14-.068.149-.15l.193-3.61-.193-3.67c-.01-.082-.072-.15-.15-.15m1.8-.863c-.01-.088-.073-.15-.15-.15-.079 0-.14.062-.152.15l-.216 4.683.216 4.623c.011.088.073.15.152.15.077 0 .14-.062.15-.15l.245-4.623-.245-4.683zm.964 9.46c.104 0 .187-.088.187-.196v-9.13c0-.108-.083-.196-.187-.196-.111 0-.189.088-.189.196v9.13c0 .108.078.196.19.196z"/>
                                        </svg>
                                    </span>
                                    <input type="url" id="soundcloud_url" name="soundcloud_url" value="{{ old('soundcloud_url', $playlist->soundcloud_url) }}"
                                           class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-primary-500 focus:border-primary-500 block flex-1 min-w-0 w-full text-sm p-2.5">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Image Upload -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="image">{{ __('Profile Image') }}</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="image" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50">
                                    <div class="relative w-full h-full">
                                        <img :src="imageUrl || currentImage" class="absolute inset-0 w-full h-full object-cover rounded-lg" alt="Preview">
                                        <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 bg-opacity-50 text-white opacity-0 hover:opacity-100 transition-opacity duration-300">
                                            <svg class="w-8 h-8 mb-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="text-sm">{{ __('Click to change image') }}</p>
                                        </div>
                                    </div>
                                    <input type="file" id="image" name="image" class="hidden" accept="image/*" @change="previewImage">
                                </label>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ $playlist->is_active ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900">{{ __('Active') }}</span>
                            </label>
                        </div>

                        <!-- Tracks Selection -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">{{ __('Select Vinyls (max 10)') }}</label>
                            <div class="mt-2 space-y-2">
                                <template x-for="(vinyl, index) in selectedVinyls" :key="index">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <span x-text="vinyl.title" class="text-sm text-gray-900"></span>
                                        <button type="button" @click="removeVinyl(index)" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        <input type="hidden" :name="'vinyls['+index+'][vinyl_master_id]'" :value="vinyl.vinyl_master_id">
                                        <input type="hidden" :name="'vinyls['+index+'][vinyl_sec_id]'" :value="vinyl.vinyl_sec_id">
                                    </div>
                                </template>
                            </div>

                            <div class="mt-4" x-show="selectedVinyls.length < 10">
                                <div class="relative" x-data="{ search: '', isOpen: false, results: [] }">
                                    <input
                                        type="text"
                                        x-model="search"
                                        @input="searchVinyls"
                                        @focus="isOpen = true"
                                        placeholder="{{ __('Search for vinyls...') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                                    >
                                    <div
                                        x-show="isOpen && results.length > 0"
                                        @click.away="isOpen = false"
                                        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg"
                                    >
                                        <ul class="max-h-60 overflow-auto">
                                            <template x-for="result in results" :key="result.id">
                                                <li>
                                                    <button
                                                        type="button"
                                                        @click="addVinyl(result); isOpen = false; search = ''"
                                                        class="w-full px-4 py-2 text-left hover:bg-gray-100"
                                                    >
                                                        <span x-text="result.title" class="block font-medium"></span>
                                                        <span x-text="result.artist" class="block text-sm text-gray-500"></span>
                                                    </button>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <button type="button" onclick="window.history.back()" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                        {{ __('Cancel') }}
                    </button>
                    <a href="{{ route('admin.playlists.edit_tracks', $playlist) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        {{ __('Editar Faixas') }}
                    </a>
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        {{ __('Update Playlist') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function playlistForm(data) {
            return {
                imageUrl: null,
                currentImage: data.currentImage,
                selectedTracks: data.tracks || [],

                previewImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imageUrl = URL.createObjectURL(file);
                    }
                },

                addTrack(event) {
                    if (this.selectedTracks.length >= 10) {
                        alert('{{ __("Maximum 10 tracks allowed") }}');
                        return;
                    }

                    const select = event.target;
                    const value = select.value;

                    if (!value) return;

                    const trackData = JSON.parse(value);
                    this.selectedTracks.push(trackData);
                    select.value = '';
                },

                removeTrack(index) {
                    this.selectedTracks.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
@endsection

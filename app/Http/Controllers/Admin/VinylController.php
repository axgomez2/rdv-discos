<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{VinylMaster, Artist, Genre, Style, Product, ProductType, RecordLabel, Track, Weight, Dimension, VinylSec, Media, CatStyleShop, Cart, Wantlist, Wishlist};
use App\Traits\FlashMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Http, DB, Storage, Log};
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class VinylController extends Controller
{
    use FlashMessages;

    // Index and listing methods
    public function index(Request $request)
    {
        $query = VinylMaster::with(['artists', 'genres', 'recordLabel', 'vinylSec']);

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('artists', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('recordLabel', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $vinyls = $query->orderBy('created_at', 'desc')
                        ->paginate(50)
                        ->withQueryString();

        return view('admin.vinyls.index', compact('vinyls'));
    }

    public function show($id)
    {
        $vinyl = VinylMaster::with([
            'artists', 'genres', 'styles', 'recordLabel', 'tracks', 'vinylSec'
        ])->findOrFail($id);

        $cardClicks = $vinyl->card_clicks ?? 0;
        $wishlistCount = $this->getWishlistCount($vinyl);
        $wantListCount = $this->getWantListCount($vinyl);
        $incompleteCartsCount = $this->getIncompleteCartsCount($vinyl);

        return view('admin.vinyls.show', compact(
            'vinyl', 'cardClicks', 'wishlistCount', 'wantListCount', 'incompleteCartsCount'
        ));
    }

    // Create and store methods
    public function create(Request $request)
    {
        try {
            $searchResults = [];
            $query = $request->input('query');
            $selectedRelease = null;

            if ($query) {
                $searchResults = $this->searchDiscogs($query);
            }

            $releaseId = $request->input('release_id');
            if ($releaseId) {
                $selectedRelease = $this->getDiscogsRelease($releaseId);
            }

            return view('admin.vinyls.create', compact('searchResults', 'query', 'selectedRelease'));
        } catch (\Exception $e) {
            Log::error('Erro ao criar vinyl: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }

    public function store(Request $request)
    {
        $releaseId = $request->input('release_id');
        $releaseData = $this->getDiscogsRelease($releaseId);

        if (!$releaseData) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch release data from Discogs.'], 400);
        }

        $existingVinyl = VinylMaster::where('discogs_id', $releaseId)->first();
        if ($existingVinyl) {
            return response()->json([
                'status' => 'exists',
                'message' => 'Este disco já está cadastrado no sistema.',
                'vinyl_id' => $existingVinyl->id
            ]);
        }

        DB::beginTransaction();

        try {
            $vinylMaster = $this->createOrUpdateVinylMaster($releaseData);
            $this->syncArtists($vinylMaster, $releaseData['artists']);
            $this->syncGenres($vinylMaster, $releaseData['genres']);
            $this->syncStyles($vinylMaster, $releaseData['styles'] ?? []);
            $this->associateRecordLabel($vinylMaster, $releaseData['labels'][0] ?? null);
            $this->createOrUpdateTracks($vinylMaster, $releaseData['tracklist']);
            $this->createOrUpdateProduct($vinylMaster, $releaseData);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Disco salvo com sucesso!',
                'vinyl_id' => $vinylMaster->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving vinyl data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro ao salvar o disco. Por favor, tente novamente.',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    // Edit and update methods
    public function edit($id)
    {
        $vinyl = VinylMaster::with('vinylSec')->findOrFail($id);
        $weights = Weight::all();
        $dimensions = Dimension::all();
        $categories = CatStyleShop::all();

        return view('admin.vinyls.edit', compact('vinyl', 'weights', 'dimensions', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $vinyl = VinylMaster::findOrFail($id);

        $validatedData = $request->validate([
            'description'         => 'nullable|string',
            'weight_id'           => 'required|exists:weights,id',
            'dimension_id'        => 'required|exists:dimensions,id',
            'quantity'            => 'required|integer|min:0',
            'price'               => 'required|numeric|min:0',
            'buy_price'           => 'nullable|numeric|min:0',
            'promotional_price'   => 'nullable|numeric|min:0',
            'is_promotional'      => 'boolean',
            'in_stock'            => 'boolean',
            'category_ids'        => 'required|array',
            'category_ids.*'      => 'exists:cat_style_shop,id',
        ]);

        DB::beginTransaction();

        try {
            $vinyl->update(['description' => $validatedData['description']]);

            $vinyl->vinylSec()->updateOrCreate(
                ['vinyl_master_id' => $vinyl->id],
                [
                    'weight_id'           => $validatedData['weight_id'],
                    'dimension_id'        => $validatedData['dimension_id'],
                    'quantity'            => $validatedData['quantity'],
                    'price'               => $validatedData['price'],
                    'buy_price'           => $validatedData['buy_price'],
                    'promotional_price'   => $validatedData['promotional_price'],
                    'is_promotional'      => $validatedData['is_promotional'] ?? false,
                    'in_stock'            => $validatedData['in_stock'] ?? false,
                ]
            );

            $vinyl->catStyleShops()->sync($validatedData['category_ids']);

            DB::commit();
            return redirect()->route('admin.vinyls.index')->with('success', 'Disco atualizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating vinyl: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withInput()->with('error', 'Ocorreu um erro ao atualizar o disco. Por favor, tente novamente.');
        }
    }

    // Delete method
    public function destroy($id)
    {
        $vinyl = VinylMaster::findOrFail($id);

        try {
            DB::beginTransaction();

            $vinyl->artists()->detach();
            $vinyl->genres()->detach();
            $vinyl->styles()->detach();
            $vinyl->tracks()->delete();

            if ($vinyl->vinylSec) {
                $vinyl->vinylSec->delete();
            }

            if ($vinyl->product) {
                $vinyl->product->delete();
            }

            if ($vinyl->cover_image) {
                Storage::disk('public')->delete($vinyl->cover_image);
            }

            foreach ($vinyl->media as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }

            $vinyl->delete();

            DB::commit();
            return redirect()->route('admin.vinyls.index')->with('success', 'Disco excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting vinyl: ' . $e->getMessage());
            return redirect()->route('admin.vinyls.index')->with('error', 'Ocorreu um erro ao excluir o disco. Por favor, tente novamente.');
        }
    }

    // Image handling methods
    public function uploadImage(Request $request, $id)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);

        $vinyl = VinylMaster::findOrFail($id);

        DB::beginTransaction();

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $coverImageName = 'vinyl_covers/' . $vinyl->id . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                Storage::disk('public')->put($coverImageName, file_get_contents($image));

                if ($vinyl->cover_image) {
                    Storage::disk('public')->delete($vinyl->cover_image);
                }

                $vinyl->cover_image = $coverImageName;
                $vinyl->save();

                DB::commit();
                return back()->with('success', 'Imagem carregada com sucesso.');
            }

            DB::rollBack();
            return back()->with('error', 'Falha ao carregar a imagem.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao fazer upload da imagem: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Ocorreu um erro ao salvar a imagem. Por favor, tente novamente.');
        }
    }

    public function fetchDiscogsImage($id)
    {
        $vinyl = VinylMaster::findOrFail($id);

        if (!$vinyl->discogs_id) {
            return back()->with('error', 'ID do Discogs não encontrado.');
        }

        DB::beginTransaction();

        try {
            $release = $this->getDiscogsRelease($vinyl->discogs_id);

            if ($release && isset($release['images'][0]['uri'])) {
                $coverImageUrl = $release['images'][0]['uri'];
                $coverImageContents = Http::get($coverImageUrl)->body();
                $coverImageName = 'vinyl_covers/' . $vinyl->id . '_' . Str::random(10) . '.jpg';

                Storage::disk('public')->put($coverImageName, $coverImageContents);

                if ($vinyl->cover_image) {
                    Storage::disk('public')->delete($vinyl->cover_image);
                }

                $vinyl->cover_image = $coverImageName;
                $vinyl->save();

                DB::commit();
                return back()->with('success', 'Imagem do Discogs importada com sucesso.');
            }

            DB::rollBack();
            return back()->with('warning', 'Nenhuma imagem encontrada no Discogs. Por favor, faça o upload manual de uma imagem.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao buscar imagem do Discogs: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Erro ao buscar imagem do Discogs. Por favor, tente fazer o upload manual de uma imagem.');
        }
    }

    public function removeImage($id)
    {
        $vinyl = VinylMaster::findOrFail($id);

        DB::beginTransaction();

        try {
            if ($vinyl->cover_image) {
                Storage::disk('public')->delete($vinyl->cover_image);
                $vinyl->cover_image = null;
                $vinyl->save();

                DB::commit();
                return back()->with('success', 'Imagem removida com sucesso.');
            }

            DB::rollBack();
            return back()->with('error', 'Nenhuma imagem para remover.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao remover imagem: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Ocorreu um erro ao remover a imagem. Por favor, tente novamente.');
        }
    }

    // Helper methods
    private function searchDiscogs($query)
    {
        $response = Http::get('https://api.discogs.com/database/search', [
            'q' => $query,
            'type' => 'release',
            'token' => config('services.discogs.token'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch data from Discogs API: ' . $response->body());
        }

        return $response->json()['results'] ?? [];
    }

    private function getDiscogsRelease($releaseId)
    {
        try {
            $response = Http::get("https://api.discogs.com/releases/{$releaseId}", [
                'token' => config('services.discogs.token'),
            ]);

            if (!$response->successful()) {
                return null;
            }

            $releaseData = $response->json();

            $marketResponse = Http::get("https://api.discogs.com/releases/{$releaseId}", [
                'token' => config('services.discogs.token'),
            ]);

            if ($marketResponse->successful()) {
                $marketData = $marketResponse->json();
                $lowestPrice = $marketData['lowest_price'] ?? 0;

                if ($lowestPrice > 0) {
                    $releaseData['lowest_price'] = $lowestPrice;
                    $releaseData['median_price'] = $lowestPrice * 1.5;
                    $releaseData['highest_price'] = $lowestPrice * 2;

                    Log::info('Calculated Prices:', [
                        'lowest' => $releaseData['lowest_price'],
                        'median' => $releaseData['median_price'],
                        'highest' => $releaseData['highest_price']
                    ]);
                }
            }

            return $releaseData;
        } catch (\Exception $e) {
            Log::error('Error fetching Discogs data: ' . $e->getMessage());
            return null;
        }
    }

    private function createOrUpdateVinylMaster($releaseData)
    {
        $coverImagePath = null;
        if (!empty($releaseData['images'][0]['uri'])) {
            $coverImageUrl = $releaseData['images'][0]['uri'];
            $coverImageContents = Http::get($coverImageUrl)->body();
            $coverImageName = 'vinyl_covers/' . $releaseData['id'] . '_' . Str::random(10) . '.jpg';
            Storage::disk('public')->put($coverImageName, $coverImageContents);
            $coverImagePath = $coverImageName;
        }

        return VinylMaster::updateOrCreate(
            ['discogs_id' => $releaseData['id']],
            [
                'title' => $releaseData['title'],
                'release_year' => $releaseData['year'],
                'country' => $releaseData['country'],
                'description' => $releaseData['notes'] ?? null,
                'cover_image' => $coverImagePath,
                'discogs_url' => $releaseData['uri'] ?? null,
            ]
        );
    }

    private function syncArtists($vinylMaster, $artists)
    {
        $artistIds = collect($artists)->map(function ($artistData) {
            $artist = Artist::updateOrCreate(
                ['name' => $artistData['name']],
                ['slug' => Str::slug($artistData['name'])]
            );
            return $artist->id;
        });

        $vinylMaster->artists()->sync($artistIds);
    }

    private function syncGenres($vinylMaster, $genres)
    {
        $genreIds = collect($genres)->map(function ($genreName) {
            $genre = Genre::updateOrCreate(
                ['name' => $genreName],
                ['slug' => Str::slug($genreName)]
            );
            return $genre->id;
        });

        $vinylMaster->genres()->sync($genreIds);
    }

    private function syncStyles($vinylMaster, $styles)
    {
        $styleIds = collect($styles)->map(function ($styleName) {
            $style = Style::updateOrCreate(
                ['name' => $styleName],
                ['slug' => Str::slug($styleName)]
            );
            return $style->id;
        });

        $vinylMaster->styles()->sync($styleIds);
    }

    private function associateRecordLabel($vinylMaster, $labelData)
    {
        if ($labelData) {
            $label = RecordLabel::updateOrCreate(
                ['name' => $labelData['name']],
                ['slug' => Str::slug($labelData['name'])]
            );
            $vinylMaster->recordLabel()->associate($label);
            $vinylMaster->save();
        }
    }

    private function createOrUpdateTracks($vinylMaster, $tracklist)
    {
        foreach ($tracklist as $trackData) {
            if (!empty($trackData['title'])) {
                Track::updateOrCreate(
                    [
                        'vinyl_master_id' => $vinylMaster->id,
                        'name' => $trackData['title'],
                    ],
                    [
                        'duration' => $trackData['duration'] ?? null,
                    ]
                );
            }
        }
    }

    private function createOrUpdateProduct($vinylMaster, $releaseData)
    {
        $productType = ProductType::where('slug', 'vinyl')->firstOrFail();

        $product = Product::updateOrCreate(
            [
                'productable_id' => $vinylMaster->id,
                'productable_type' => 'App\\Models\\VinylMaster',
            ],
            [
                'name' => $releaseData['title'],
                'slug' => Str::slug($releaseData['title']),
                'description' => $releaseData['notes'] ?? null,
                'product_type_id' => $productType->id,
            ]
        );

        return $product;
    }

    private function getWishlistCount($vinyl)
    {
        return Wishlist::where('product_id', $vinyl->id)
            ->where('product_type', 'VinylMaster')
            ->count();
    }

    private function getWantListCount($vinyl)
    {
        if (!$vinyl->vinylSec->in_stock) {
            return Wantlist::where('product_id', $vinyl->id)
                ->where('product_type', 'VinylMaster')
                ->count();
        }
        return 0;
    }

    private function getIncompleteCartsCount($vinyl)
    {
        return DB::table('carts')
            ->join('cart_items', 'carts.id', '=', 'cart_items.cart_id')
            ->where('cart_items.product_id', $vinyl->id)
            ->whereRaw('carts.updated_at > DATE_SUB(NOW(), INTERVAL 1 DAY)')
            ->distinct('carts.id')
            ->count();
    }

    public function complete($id)
    {
        try {
            $vinylMaster = VinylMaster::with('vinylSec.categories', 'tracks')->findOrFail($id);
            $weights = \App\Models\Weight::all();
            $dimensions = \App\Models\Dimension::all();
            $categories = \App\Models\CatStyleShop::all();
            $selectedCategories = $vinylMaster->vinylSec ? $vinylMaster->vinylSec->categories->pluck('id')->toArray() : [];
            $tracks = $vinylMaster->tracks;

            // Se já existir um vinylSec, carregamos o supplier existente, caso contrário será null
            $supplier = $vinylMaster->vinylSec ? $vinylMaster->vinylSec->supplier : null;

            return view('admin.vinyls.complete', compact('vinylMaster', 'weights', 'dimensions', 'categories', 'selectedCategories', 'tracks', 'supplier'));
        } catch (\Exception $e) {
            Log::error('Error loading vinyl completion form: ' . $e->getMessage());
            return redirect()->route('admin.vinyls.index')->with('error', 'Não foi possível carregar o formulário de finalização do vinyl. Por favor, tente novamente.');
        }
    }
    public function storeComplete(Request $request, $id)
    {
        $vinylMaster = VinylMaster::with('tracks')->findOrFail($id);

        $validatedData = $request->validate([
            'catalog_number'       => 'nullable|string',
            'barcode'              => 'nullable|string',
            'weight_id'            => 'required|exists:weights,id',
            'dimension_id'         => 'required|exists:dimensions,id',
            'quantity'             => 'required|integer|min:0',
            'price'                => 'required|numeric|min:0',
            'format'               => 'nullable|string',
            'num_discs'            => 'required|integer|min:1',
            'speed'                => 'nullable|string',
            'edition'              => 'nullable|string',
            'notes'                => 'nullable|string',
            'is_new'               => 'required|boolean',
            'buy_price'            => 'nullable|numeric|min:0',
            'promotional_price'    => 'nullable|numeric|min:0',
            'is_promotional'       => 'required|boolean',
            'in_stock'             => 'required|boolean',
            'cover_status'         => 'nullable|in:mint,near_mint,very_good,good,fair,poor,generic',
            'midia_status'         => 'nullable|in:mint,near_mint,very_good,good,fair,poor',
            'track_youtube_urls'   => 'nullable|array',
            'track_youtube_urls.*' => 'nullable|url',
            'category_ids'         => 'required|array',
            'category_ids.*'       => 'exists:cat_style_shop,id',
            'supplier'             => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Cria o VinylSec e vincula ao VinylMaster
            $vinylSec = new VinylSec();
            $vinylSec->fill($validatedData);
            $vinylSec->vinyl_master_id = $vinylMaster->id;
            $vinylSec->save();

            // Sincroniza as categorias
            $vinylMaster->catStyleShops()->sync($validatedData['category_ids']);

            // Atualiza as URLs do YouTube dos tracks, se enviadas
            if (isset($validatedData['track_youtube_urls'])) {
                foreach ($validatedData['track_youtube_urls'] as $trackId => $youtubeUrl) {
                    $track = $vinylMaster->tracks->find($trackId);
                    if ($track) {
                        $track->youtube_url = $youtubeUrl ?: null;
                        $track->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.vinyls.index')
                             ->with('success', 'Vinyl finalizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing vinyl record: ' . $e->getMessage());
            return redirect()->back()->withInput()
                             ->withErrors(['error' => 'Ocorreu um erro ao salvar o vinyl. Por favor, tente novamente.']);
        }
    }

    public function updateField(Request $request)
{
    try {
        $vinyl = VinylMaster::findOrFail($request->id);

        if (!$vinyl->vinylSec) {
            throw new \Exception('Vinyl não possui dados secundários cadastrados.');
        }

        $vinyl->vinylSec->update([
            $request->field => $request->value
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campo atualizado com sucesso'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}
}

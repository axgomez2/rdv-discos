<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deejay;
use App\Models\VinylMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DJController extends Controller
{
    public function index()
    {
        $djs = Deejay::all();
        return view('admin.djs.index', compact('djs'));
    }

    public function create()
    {
        $vinyls = VinylMaster::all();
        return view('admin.djs.create', compact('vinyls'));
    }

    private function handleImageUpload(Request $request, Deejay $dj = null)
    {
        if ($request->hasFile('image')) {
            if ($dj && $dj->image) {
                Storage::disk('public')->delete($dj->image);
            }
            return $request->file('image')->store('dj_images', 'public');
        }
        return null;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'social_media' => 'nullable|string|max:255',
            'bio' => 'required|string',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        DB::beginTransaction();

        try {
            $dj = new Deejay();
            $dj->name = $validatedData['name'];
            $dj->slug = Str::slug($validatedData['name']);
            $dj->social_media = $validatedData['social_media'];
            $dj->bio = $validatedData['bio'];
            $dj->is_active = true; // Set as active by default

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('dj_images', 'public');
                $dj->image = $path;
            }

            $dj->save();

            DB::commit();

            return redirect()->route('admin.djs.index')->with('success', 'DJ criado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error message for debugging
            \Log::error('Erro ao criar DJ: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao criar DJ: ' . $e->getMessage());
        }
    }

    public function show(Deejay $dj)
    {
        $recommendations = $dj->recommendations()->orderBy('order')->get();
        return view('admin.djs.show', compact('dj', 'recommendations'));
    }

    public function edit(Deejay $dj)
    {
        $vinyls = VinylMaster::all();
        $recommendations = $dj->recommendations()->orderBy('order')->pluck('vinyl_masters.id')->toArray();
        return view('admin.djs.edit', compact('dj', 'vinyls', 'recommendations'));
    }

    public function update(Request $request, Deejay $dj)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'social_media' => 'nullable|string|max:255',
            'bio' => 'required|string',
            'image' => 'nullable|image|max:2048', // 2MB Max
            'recommendations' => 'required|array|min:1',
            'recommendations.*' => 'exists:vinyl_masters,id',
        ]);

        DB::beginTransaction();

        try {
            $dj->name = $validatedData['name'];
            $dj->slug = Str::slug($validatedData['name']);
            $dj->social_media = $validatedData['social_media'];
            $dj->bio = $validatedData['bio'];

        $dj->image = $this->handleImageUpload($request, $dj);

            $dj->save();

            $dj->recommendations()->detach();
            foreach ($validatedData['recommendations'] as $index => $vinylId) {
                $dj->recommendations()->attach($vinylId, ['order' => $index + 1]);
            }

            DB::commit();

            return redirect()->route('admin.djs.index')->with('success', 'DJ atualizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error message for debugging
            \Log::error('Erro ao atualizar DJ: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao atualizar DJ: ' . $e->getMessage());
        }
    }

    public function destroy(Deejay $dj)
    {
        if ($dj->image) {
            Storage::disk('public')->delete($dj->image);
        }
        $dj->recommendations()->detach();
        $dj->delete();
        return redirect()->route('admin.djs.index')->with('success', 'DJ excluído com sucesso.');
    }

    public function toggleActive(Deejay $dj)
    {
        $dj->is_active = !$dj->is_active;
        $dj->save();

        return redirect()->route('admin.djs.index')->with('success', 'Status do DJ atualizado com sucesso.');
    }

    public function manageVinyls(Deejay $dj)
    {
        $recommendations = $dj->recommendations()->orderBy('order')->get();
        $allVinyls = VinylMaster::with('artists')->paginate(20);
        return view('admin.djs.manage-vinyls', compact('dj', 'recommendations', 'allVinyls'));
    }

    public function updateRecommendations(Request $request, Deejay $dj)
    {
        $validatedData = $request->validate([
            'recommendations' => 'required|array|max:10',
            'recommendations.*.id' => 'required|exists:vinyl_masters,id',
            'recommendations.*.order' => 'required|integer|min:1|max:10',
        ]);

        DB::transaction(function () use ($dj, $validatedData) {
            $dj->recommendations()->detach();

            foreach ($validatedData['recommendations'] as $recommendation) {
                $dj->recommendations()->attach($recommendation['id'], ['order' => $recommendation['order']]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function searchVinyls(Request $request)
    {
        $query = $request->get('query');

        $vinyls = VinylMaster::with('artists')
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhereHas('artists', function($q) use ($query) {
                      $q->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->paginate(10); // Implement pagination

        return response()->json($vinyls);
    }
}

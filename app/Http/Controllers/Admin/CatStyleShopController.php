<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatStyleShop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatStyleShopController extends Controller
{
    public function index()
    {
        $categories = CatStyleShop::all();
        return view('admin.settings.cat_style_shop', compact('categories'));
    }

    public function create()
    {
        return view('admin.settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:255|unique:cat_style_shop,nome',
        ]);

        $category = CatStyleShop::create([
            'nome' => $request->nome,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->nome),
            'parent_id' => $request->parent_id ?? null,
        ]);

        // Se a requisição for AJAX, retorna uma resposta JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoria criada com sucesso!',
                'category' => $category
            ]);
        }

        // Caso contrário, redireciona normalmente
        return redirect()->route('admin.cat-style-shop.index')->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(CatStyleShop $catStyleShop)
    {
        return view('admin.settings.edit', compact('catStyleShop'));
    }

    public function update(Request $request, CatStyleShop $catStyleShop)
    {
        $request->validate([
            'nome' => 'required|max:255|unique:cat_style_shop,nome,' . $catStyleShop->id,
        ]);

        $catStyleShop->update([
            'nome' => $request->nome,
            'slug' => Str::slug($request->nome),
        ]);

        return redirect()->route('admin.cat-style-shop.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(CatStyleShop $catStyleShop)
    {
        $catStyleShop->delete();
        return redirect()->route('admin.cat-style-shop.index')->with('success', 'Categoria excluída com sucesso!');
    }
}

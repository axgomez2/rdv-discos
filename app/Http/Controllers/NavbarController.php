<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category; // supondo que este seja o model das categorias

class NavbarController extends Controller
{
    public function index()
    {
        // Busque as categorias ou outros dados necessários
        $categories = Category::all();

        // Se houver outras informações, busque e passe para a view

        return view('partials.navbar', compact('categories'));
    }
}

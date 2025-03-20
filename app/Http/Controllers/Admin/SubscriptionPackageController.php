<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;

class SubscriptionPackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::latest()->paginate(10);
        return view('admin.subscription-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.subscription-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'vinyl_quantity' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        SubscriptionPackage::create($validated);

        return redirect()
            ->route('admin.subscription-packages.index')
            ->with('success', 'Pacote de assinatura criado com sucesso!');
    }

    public function edit(SubscriptionPackage $package)
    {
        return view('admin.subscription-packages.edit', compact('package'));
    }

    public function update(Request $request, SubscriptionPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'vinyl_quantity' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $package->update($validated);

        return redirect()
            ->route('admin.subscription-packages.index')
            ->with('success', 'Pacote de assinatura atualizado com sucesso!');
    }

    public function destroy(SubscriptionPackage $package)
    {
        if ($package->subscriptions()->exists()) {
            return redirect()
                ->route('admin.subscription-packages.index')
                ->with('error', 'Não é possível excluir um pacote que possui assinaturas ativas.');
        }

        $package->delete();

        return redirect()
            ->route('admin.subscription-packages.index')
            ->with('success', 'Pacote de assinatura excluído com sucesso!');
    }

    public function toggleStatus(SubscriptionPackage $package)
    {
        $package->update(['is_active' => !$package->is_active]);

        return redirect()
            ->route('admin.subscription-packages.index')
            ->with('success', 'Status do pacote atualizado com sucesso!');
    }
}

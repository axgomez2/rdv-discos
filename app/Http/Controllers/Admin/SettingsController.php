<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Weight;
use App\Models\Dimension;
use App\Models\Brand;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $weights = Weight::all();
        $dimensions = Dimension::all();
        $brands = Brand::all();
        $equipmentCategories = EquipmentCategory::all();

        return view('admin.settings.index', compact('weights', 'dimensions', 'brands', 'equipmentCategories'));
    }

    public function storeWeight(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|numeric',
            'unit' => 'required|string|max:50',
        ]);

        $weight = Weight::create($validatedData);

        return response()->json($weight);
    }

    public function updateWeight(Request $request, Weight $weight)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|numeric',
            'unit' => 'required|string|max:50',
        ]);

        $weight->update($validatedData);

        return response()->json(['message' => 'Weight updated successfully.']);
    }

    public function deleteWeight(Weight $weight)
    {
        $weight->delete();

        return response()->json(['message' => 'Weight deleted successfully.']);
    }

    public function storeDimension(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'height' => 'required|numeric',
            'width' => 'required|numeric',
            'depth' => 'required|numeric',
            'unit' => 'required|string|max:50',
        ]);

        $dimension = Dimension::create($validatedData);

        return response()->json($dimension);
    }

    public function updateDimension(Request $request, Dimension $dimension)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'height' => 'required|numeric',
            'width' => 'required|numeric',
            'depth' => 'required|numeric',
            'unit' => 'required|string|max:50',
        ]);

        $dimension->update($validatedData);

        return response()->json(['message' => 'Dimension updated successfully.']);
    }

    public function deleteDimension(Dimension $dimension)
    {
        $dimension->delete();

        return response()->json(['message' => 'Dimension deleted successfully.']);
    }

    public function storeBrand(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::create($validatedData);

        return response()->json($brand);
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $brand->update($validatedData);

        return response()->json(['message' => 'Brand updated successfully.']);
    }

    public function deleteBrand(Brand $brand)
    {
        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully.']);
    }

    public function storeEquipmentCategory(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:equipment_categories,id',
            'description' => 'nullable|string',
        ]);

        $equipmentCategory = EquipmentCategory::create($validatedData);

        return response()->json($equipmentCategory);
    }

    public function updateEquipmentCategory(Request $request, EquipmentCategory $equipmentCategory)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:equipment_categories,id',
            'description' => 'nullable|string',
        ]);

        $equipmentCategory->update($validatedData);

        return response()->json(['message' => 'Equipment Category updated successfully.']);
    }

    public function deleteEquipmentCategory(EquipmentCategory $equipmentCategory)
    {
        $equipmentCategory->delete();

        return response()->json(['message' => 'Equipment Category deleted successfully.']);
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return response()->json(Brand::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:brands,name|max:100',
            'country' => 'nullable|max:100',
            'estado' => 'boolean'
        ]);

        $brand = Brand::create($validated);
        return response()->json($brand, 201);
    }

    public function show(string $id)
    {
        $brand = Brand::find($id);
        return $brand ? response()->json($brand, 200) : response()->json(['message' => 'Marca no encontrada'], 404);
    }

    public function update(Request $request, string $id)
    {
        $brand = Brand::find($id);
        if (!$brand) return response()->json(['message' => 'Marca no encontrada'], 404);

        $validated = $request->validate([
            'name' => "sometimes|required|max:100|unique:brands,name,{$id}",
            'country' => 'nullable|max:100',
            'estado' => 'boolean'
        ]);

        $brand->update($validated);
        return response()->json($brand, 200);
    }

    public function destroy(string $id)
    {
        $brand = Brand::find($id);
        if (!$brand) return response()->json(['message' => 'Marca no encontrada'], 404);
        
        $brand->delete();
        return response()->json(['message' => 'Marca eliminada'], 200);
    }
}
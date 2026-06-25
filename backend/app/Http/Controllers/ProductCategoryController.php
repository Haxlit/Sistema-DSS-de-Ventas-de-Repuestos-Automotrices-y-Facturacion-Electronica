<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        return response()->json(ProductCategory::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:product_categories,name|max:100',
            'description' => 'nullable',
            'estado' => 'boolean'
        ]);

        $category = ProductCategory::create($validated);
        return response()->json($category, 201);
    }

    public function show(string $id)
    {
        $category = ProductCategory::find($id);
        return $category ? response()->json($category, 200) : response()->json(['message' => 'Categoría no encontrada'], 404);
    }

    public function update(Request $request, string $id)
    {
        $category = ProductCategory::find($id);
        if (!$category) return response()->json(['message' => 'Categoría no encontrada'], 404);

        $validated = $request->validate([
            'name' => "sometimes|required|max:100|unique:product_categories,name,{$id}",
            'description' => 'nullable',
            'estado' => 'boolean'
        ]);

        $category->update($validated);
        return response()->json($category, 200);
    }

    public function destroy(string $id)
    {
        $category = ProductCategory::find($id);
        if (!$category) return response()->json(['message' => 'Categoría no encontrada'], 404);

        $category->delete();
        return response()->json(['message' => 'Categoría eliminada'], 200);
    }
}
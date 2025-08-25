<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;
use App\Models\Attribute;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $attributes = Attribute::orderBy('name')->get();
        return view('categories.create', compact('attributes')); // <-- 3. ENVÍALOS A LA VISTA
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'attributes' => 'nullable|array',
            'attributes.*' => 'integer|exists:attributes,id'
        ]);

        // 1. Crear la categoría SOLO con sus propios datos.
        $category = Category::create([
            'name' => $validatedData['name']
        ]);

        // 2. Sincronizar la relación de atributos por separado.
        if ($request->has('attributes')) {
            $category->attributes()->sync($request->input('attributes'));
        }

        return redirect()->route('categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
         $attributes = Attribute::orderBy('name')->get();
        return view('categories.edit', compact('category', 'attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'attributes' => 'nullable|array',
            'attributes.*' => 'integer|exists:attributes,id'
        ]);

        // 1. Actualizar la categoría SOLO con sus propios datos.
        $category->update([
            'name' => $validatedData['name']
        ]);

        // 2. Sincronizar la relación de atributos por separado.
        $category->attributes()->sync($request->input('attributes', []));

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {

    // if ($category->products()->count() > 0) {
    //     return back()->with('error', 'No se puede eliminar una categoría que tiene productos asignados.');
    // }

    $category->delete();

    return redirect()->route('categories.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}

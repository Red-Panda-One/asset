<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriesRequest;
use App\Http\Requests\UpdateCategoriesRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Categories;
use Inertia\Inertia;
use App\Models\Team;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Categories/Index/Index', [
            'categories' => CategoryResource::collection(
                Categories::where('team_id', request()->user()->currentTeam->id)
                    ->paginate(20)
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Team $team)
    {
        return Inertia::render('Categories/Edit/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoriesRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category = Categories::create([
            ...$validated,
            'team_id' => $request->user()->currentTeam->id
        ]);

        return redirect()->route('categories.index')->with('flash', [
            'banner' => 'Category created successfully.',
            'bannerStyle' => 'success',
            'bannerTimeout' => 2000,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categories $categories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categories $category)
    {
        return Inertia::render('Categories/Edit/Index', [
            'category' => new CategoryResource($category)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoriesRequest $request, Categories $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('flash', [
            'banner' => 'Tag updated successfully.',
            'bannerStyle' =>'success',
            'bannerTimeout' => 2000,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categories $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('flash', [
            'banner' => 'Tag deleted successfully.',
            'bannerStyle' =>'success',
            'bannerTimeout' => 2000,
        ]);
    }
}

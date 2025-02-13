<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\TagResource;
use App\Models\Asset;
use App\Models\Categories;
use App\Models\Location;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Team;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Assets/Index', [
            'assets' => AssetResource::collection(
                Asset::with(['category', 'location', 'tags'])  // Make sure 'tags' is included here
                    ->where('team_id', $request->user()->currentTeam->id)
                    ->paginate(20)
            ),
            'filters' => request()->all(['search', 'per_page'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Assets/Edit/Create', [
            'categories' => CategoryResource::collection(
                Categories::where('team_id', request()->user()->currentTeam->id)->get()
            ),
            'locations' => LocationResource::collection(
                Location::where('team_id', request()->user()->currentTeam->id)->get()
            ),
            'tags' => TagResource::collection(
                Tag::where('team_id', request()->user()->currentTeam->id)->get()
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'category_id' => 'nullable|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $data = $request->only(['name', 'description', 'value', 'category_id', 'location_id']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('assets', 'public');
            $data['image'] = $path;
        }

        $asset = Asset::create([
            ...$data,
            'team_id' => $request->user()->currentTeam->id
        ]);

        if ($request->has('tags')) {
            $asset->tags()->sync($request->tags);
        }

        return redirect()->route('assets.index')->with('flash', [
            'banner' => 'Asset created successfully.',
            'bannerStyle' => 'success',
            'bannerTimeout' => 2000,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        return Inertia::render('Assets/Edit/Index', [
            'asset' => new AssetResource($asset->load(['category', 'location', 'tags'])),
            'categories' => CategoryResource::collection(
                Categories::where('team_id', request()->user()->currentTeam->id)->get()
            ),
            'locations' => LocationResource::collection(
                Location::where('team_id', request()->user()->currentTeam->id)->get()
            ),
            'tags' => TagResource::collection(
                Tag::where('team_id', request()->user()->currentTeam->id)->get()
            ),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'category_id' => 'nullable|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $data = $request->only(['name', 'description', 'value', 'category_id', 'location_id']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('assets', 'public');
            $data['image'] = $path;
        }

        $asset->update($data);

        if ($request->has('tags')) {
            $asset->tags()->sync($request->tags);
        }

        return redirect()->route('assets.index')->with('flash', [
            'banner' => 'Asset updated successfully.',
            'bannerStyle' => 'success',
            'bannerTimeout' => 2000,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $asset->tags()->detach();
        $asset->delete();

        return redirect()->route('assets.index')->with('flash', [
            'banner' => 'Asset deleted successfully.',
            'bannerStyle' => 'success',
            'bannerTimeout' => 2000,
        ]);
    }
}

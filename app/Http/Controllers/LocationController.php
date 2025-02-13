<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Locations/Index', [
            'tags' => LocationResource::collection(
                Location::where('team_id', $request->user()->currentTeam->id)
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
        return Inertia::render('Locations/Edit/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $data = $request->only(['name', 'description', 'address']);


        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('locations', 'public');
            $data['image'] = $path;
        }

        $location = Location::create([
            ...$data,
            'team_id' => $request->user()->currentTeam->id
        ]);

        return redirect()->route('locations.index')->with('flash', [
            'banner' => 'Tag updated successfully.',
            'bannerStyle' =>'success',
            'bannerTimeout' => 2000,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        //
    }
}

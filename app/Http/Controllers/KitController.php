<?php

namespace App\Http\Controllers;

use App\Http\Resources\KitResource;
use App\Models\Kit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class KitController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Kits/Index', [
            'kits' => KitResource::collection(
                Kit::where('team_id', $request->user()->currentTeam->id)
                    ->paginate(20)
            ),
            'filters' => request()->all(['search', 'per_page'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Kits/Edit/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $kitData = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('kits', 'public');
            $kitData['image'] = $path;
        }

        $kit = Kit::create([
            ...$kitData,
            'team_id' => $request->user()->currentTeam->id
        ]);

        return redirect()->route('kits.index')->with('flash', [
            'banner' => 'Tag updated successfully.',
            'bannerStyle' =>'success',
            'bannerTimeout' => 2000,
        ]);
    }

    public function show(Kit $kit)
    {
        return Inertia::render('Kits/Show', [
            'kit' => new KitResource($kit)
        ]);
    }

    public function edit(Kit $kit)
    {
        return Inertia::render('Kits/Edit/Index', [
            'kit' => new KitResource($kit)
        ]);
    }

    public function update(Request $request, Kit $kit)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096']
        ]);


        $kitData = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('kits', 'public');
            $kitData['image'] = $path;
        }

        $kit->update($kitData);

        return redirect()->route('kits.index')->with('flash', [
            'banner' => 'Kit updated successfully.',
            'bannerStyle' => 'success',
            'bannerTimeout' => 2000,
        ]);
    }

    public function destroy(Kit $kit)
    {
        if ($kit->image) {
            Storage::disk('public')->delete($kit->image);
        }
        $kit->delete();

        return redirect()->route('kits.index');
    }
}

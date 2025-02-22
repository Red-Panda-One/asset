<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetResource;
use App\Http\Resources\KitResource;
use App\Models\Asset;
use App\Models\Kit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            'kit' => new KitResource($kit),
            'assets' => AssetResource::collection(
                Asset::where('team_id', request()->user()->currentTeam->id)->get()
            ),
            'kitAssets' => AssetResource::collection($kit->assets),
            'unavailableAssets' => DB::table('kit_asset')
                ->join('kits', 'kit_asset.kit_id', '=', 'kits.id')
                ->where('kits.team_id', request()->user()->currentTeam->id)
                ->select('kit_asset.kit_id', 'kit_asset.asset_id')
                ->get(),
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

    public function addAsset(Request $request, Kit $kit)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        // Ensure the asset belongs to the same team as the kit
        if ($asset->team_id !== $request->user()->currentTeam->id) {
            return redirect()->route('kits.show', $kit)->with('flash', [
                'banner' => 'Asset does not belong to the same team.',
                'bannerStyle' =>'danger',
                'bannerTimeout' => 2000,
            ]);
        }

        // Attach the asset to the kit if not already attached
        if (!$kit->assets()->where('asset_id', $asset->id)->exists()) {
            $kit->assets()->attach($asset->id);
            $kit->increment('asset_count');
        }

        return redirect()->route('kits.show', $kit)->with('flash', [
            'banner' => 'Asset added to kit successfully.',
            'bannerStyle' =>'success',
            'bannerTimeout' => 2000,
        ]);
    }

    public function removeAsset(Kit $kit, Asset $asset)
    {
        // Find and delete the specific record from the pivot table
        if ($kit->assets()->where('asset_id', $asset->id)->exists()) {
            $kit->assets()->detach($asset->id);
            $kit->decrement('asset_count');
        }

        return redirect()->route('kits.show', $kit)->with('flash', [
            'banner' => 'Asset removed from kit successfully.',
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

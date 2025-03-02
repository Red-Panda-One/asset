<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdditionalFileResource;
use App\Http\Resources\AssetResource;
use App\Http\Resources\KitResource;
use App\Models\AdditionalFile;
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
            'custom_id' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'additional_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'status' => 'required|string|max:255',
        ]);

        $kitData = $request->only(['name', 'description', 'status', 'custom_id']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('kits', 'public');
            $kitData['image'] = $path;
        }

        $kit = Kit::create([
            ...$kitData,
            'team_id' => $request->user()->currentTeam->id
        ]);

        // Handle additional files
        // In the store method:
        if ($request->hasFile('additional_files')) {
            foreach ($request->file('additional_files') as $file) {
                $path = $file->store('additional-files', 'public');
                $additionalFile = AdditionalFileController::create([
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'team_id' => $request->user()->currentTeam->id  // Add this line
                ]);
                $kit->additionalFiles()->attach($additionalFile->id);
                Log::info('Additional file uploaded successfully', ['path' => $path]);
            }
        }

        return redirect()->route('kits.index')->with('flash', [
            'banner' => 'Tag updated successfully.',
            'bannerStyle' =>'success',
            'bannerTimeout' => 2000,
        ]);
    }

    public function show(Kit $kit)
    {
        return Inertia::render('Kits/Show', [
            'kit' => new KitResource($kit->load(['additionalFiles'])),
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
            'kit' => new KitResource($kit->load('additionalFiles'))
        ]);
    }

    public function update(Request $request, Kit $kit)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'string', 'max:255'],
            'additional_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $kitData = $request->only(['name', 'description', 'status']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('kits', 'public');
            $kitData['image'] = $path;
        }

        $kit->update($kitData);

        // Handle removed files
        $removedFileIds = $request->input('remove_files', []);
        if (!is_array($removedFileIds)) {
            $removedFileIds = [];
        }

        if (!empty($removedFileIds)) {
            foreach ($removedFileIds as $fileId) {
                $file = AdditionalFile::find($fileId);
                if ($file) {
                    // Delete the physical file
                    Storage::disk('public')->delete($file->file_path);
                    // Detach and delete the file record
                    $kit->additionalFiles()->detach($fileId);
                    $file->delete();
                    Log::info('File removed successfully', ['file_id' => $fileId]);
                }
            }
        }

        // Handle additional files
        if ($request->hasFile('additional_files')) {
            foreach ($request->file('additional_files') as $file) {
                $path = $file->store('additional-files', 'public');
                $additionalFile = AdditionalFile::create([
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'team_id' => $request->user()->currentTeam->id  // Add this line
                ]);
                $kit->additionalFiles()->attach($additionalFile->id);
                Log::info('Additional file uploaded successfully', ['path' => $path]);
            }
        }

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

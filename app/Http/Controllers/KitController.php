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
        return Inertia::render('Kits/Edit/Create', [
            // Add availableFiles to the create view
            'availableFiles' => AdditionalFileResource::collection(
                AdditionalFile::where('team_id', request()->user()->currentTeam->id)->get()
            ),
        ]);
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
            'selected_files' => 'nullable|array',
            'selected_files.*' => 'exists:additional_files,id',
        ]);

        $kitData = $request->only(['name', 'description', 'status', 'custom_id']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('kits', 'public');
            $kitData['image'] = $path;
            Log::info('Kit image uploaded successfully', ['path' => $path]);
        }

        $kit = Kit::create([
            ...$kitData,
            'team_id' => $request->user()->currentTeam->id
        ]);

        // Handle linking existing files
        if ($request->has('selected_files')) {
            foreach ($request->selected_files as $fileId) {
                $file = AdditionalFile::find($fileId);
                if ($file) {
                    $kit->additionalFiles()->attach($fileId);
                    $file->increment('linked_count');
                    Log::info('Existing file linked to kit successfully', ['file_id' => $fileId]);
                }
            }
        }

        // Handle new file uploads
        if ($request->hasFile('additional_files')) {
            foreach ($request->file('additional_files') as $file) {
                $path = $file->store('additional-files', 'public');
                $additionalFile = AdditionalFile::create([
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'team_id' => $request->user()->currentTeam->id,
                    'linked_count' => 1
                ]);
                $kit->additionalFiles()->attach($additionalFile->id);
                Log::info('New file uploaded and linked to kit successfully', [
                    'path' => $path,
                    'file_id' => $additionalFile->id
                ]);
            }
        }

        return redirect()->route('kits.index')->with('flash', [
            'banner' => 'Kit created successfully.',
            'bannerStyle' => 'success',
            'bannerTimeout' => 2000,
        ]);
    }

    public function edit(Kit $kit)
    {
        return Inertia::render('Kits/Edit/Index', [
            'kit' => new KitResource($kit->load(['additionalFiles'])),
            'availableFiles' => AdditionalFileResource::collection(
                AdditionalFile::where('team_id', request()->user()->currentTeam->id)->get()
            ),
        ]);
    }

    public function update(Request $request, Kit $kit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'custom_id' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'additional_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'status' => 'required|string|max:255',
            'selected_files' => 'nullable|array',
            'selected_files.*' => 'exists:additional_files,id',
            'remove_files.*' => 'nullable|exists:additional_files,id',
        ]);

        // Get current additional files before any changes
        $currentFileIds = $kit->additionalFiles->pluck('id')->toArray();

        $kitData = $request->only(['name', 'description', 'status', 'custom_id']);

        if ($request->hasFile('image')) {
            if ($kit->image) {
                Storage::disk('public')->delete($kit->image);
            }
            $path = $request->file('image')->store('kits', 'public');
            $kitData['image'] = $path;
        }

        $kit->update($kitData);

        // Update additional files with linked_count management
        if ($request->has('selected_files')) {
            // Get files that will be newly attached
            $newFileIds = array_diff($request->selected_files, $currentFileIds);
            // Get files that will be detached
            $detachedFileIds = array_diff($currentFileIds, $request->selected_files);

            // Increment linked_count for newly attached files
            if (!empty($newFileIds)) {
                AdditionalFile::whereIn('id', $newFileIds)->increment('linked_count');
            }

            // Decrement linked_count for detached files
            if (!empty($detachedFileIds)) {
                AdditionalFile::whereIn('id', $detachedFileIds)->decrement('linked_count');
            }

            // Perform the sync operation
            $kit->additionalFiles()->sync($request->selected_files);
        }

        // Handle removed files
        $removedFileIds = $request->input('remove_files', []);
        if (!empty($removedFileIds)) {
            foreach ($removedFileIds as $fileId) {
                $file = AdditionalFile::find($fileId);
                if ($file) {
                    Storage::disk('public')->delete($file->file_path);
                    $kit->additionalFiles()->detach($fileId);
                    $file->decrement('linked_count');
                    if ($file->linked_count <= 0) {
                        $file->delete();
                    }
                }
            }
        }

        // Handle new file uploads
        if ($request->hasFile('additional_files')) {
            foreach ($request->file('additional_files') as $file) {
                $path = $file->store('additional-files', 'public');
                $additionalFile = AdditionalFile::create([
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'team_id' => $request->user()->currentTeam->id,
                    'linked_count' => 1
                ]);
                $kit->additionalFiles()->attach($additionalFile->id);
            }
        }

        return redirect()->route('kits.show', $kit)->with('flash', [
            'banner' => 'Kit updated successfully.',
            'bannerStyle' => 'success',
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

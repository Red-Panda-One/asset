<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdditionalFileResource;
use App\Http\Resources\AssetResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\TagResource;
use App\Models\AdditionalFile;
use App\Models\Asset;
use App\Models\Categories;
use App\Models\Location;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Team;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Assets/Index', [
            'assets' => AssetResource::collection(
                Asset::with(['category', 'location', 'tags'])
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
            'custom_id' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'additional_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'category_id' => 'nullable|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|string|max:255',
        ]);

        $data = $request->only(['name', 'description', 'value', 'category_id', 'location_id', 'status', 'custom_id']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('assets', 'public');
            $data['image'] = $path;
            Log::info('Image uploaded successfully', ['path' => $path]);
        } else {
            Log::info('No image file provided');
        }

        $asset = Asset::create([
            ...$data,
            'team_id' => $request->user()->currentTeam->id
        ]);

        if ($request->has('tags')) {
            $asset->tags()->sync($request->tags);
        }

        // Handle removed files
        $submittedFileIds = $request->input('existing_files', []);
        $currentFileIds = $asset->additionalFiles->pluck('id')->toArray();
        $removedFileIds = array_diff($currentFileIds, $submittedFileIds);

        if (!empty($removedFileIds)) {
            foreach ($removedFileIds as $fileId) {
                $file = AdditionalFile::find($fileId);
                if ($file) {
                    // Delete the physical file
                    Storage::disk('public')->delete($file->file_path);
                    // Detach and delete the file record
                    $asset->additionalFiles()->detach($fileId);
                    $file->delete();
                    Log::info('File removed successfully', ['file_id' => $fileId]);
                }
            }
        }

        // Handle additional files with linked_count
        if ($request->hasFile('additional_files')) {
            foreach ($request->file('additional_files') as $file) {
                $path = $file->store('additional-files', 'public');
                $additionalFile = AdditionalFile::create([
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'team_id' => $request->user()->currentTeam->id,
                    'linked_count' => 1  // Set initial linked_count to 1
                ]);
                $asset->additionalFiles()->attach($additionalFile->id);
                Log::info('Additional file uploaded successfully with linked_count', [
                    'path' => $path,
                    'linked_count' => 1
                ]);
            }
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
        return Inertia::render('Assets/Show', [
            'asset' => new AssetResource($asset->load(['category', 'location', 'tags', 'additionalFiles'])),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        return Inertia::render('Assets/Edit/Index', [
            'asset' => new AssetResource($asset->load(['category', 'location', 'tags', 'additionalFiles'])),
            'categories' => CategoryResource::collection(
                Categories::where('team_id', request()->user()->currentTeam->id)->get()
            ),
            'locations' => LocationResource::collection(
                Location::where('team_id', request()->user()->currentTeam->id)->get()
            ),
            'tags' => TagResource::collection(
                Tag::where('team_id', request()->user()->currentTeam->id)->get()
            ),
            'selectedTags' => TagResource::collection($asset->tags),
            'availableFiles' => AdditionalFileResource::collection(
                AdditionalFile::where('team_id', request()->user()->currentTeam->id)->get()
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
            'status' => 'required|string|max:255',
            'additional_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'remove_files.*' => 'nullable|exists:additional_files,id',
            'selected_files' => 'nullable|array',
            'selected_files.*' => 'exists:additional_files,id',
        ]);

        // Get current additional files before any changes
        $currentFileIds = $asset->additionalFiles->pluck('id')->toArray();

        $data = $request->only(['name', 'description', 'value', 'category_id', 'location_id', 'status']);

        Log::info('Extracted data for asset update', ['data' => $data]);


        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Log::info('New image file detected', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'error' => $file->getError(),
                'is_valid' => $file->isValid()
            ]);

            if ($file->isValid()) {
                // Delete old image if exists
                if ($asset->image) {
                    Log::info('Deleting old image', ['old_image_path' => $asset->image]);
                    Storage::disk('public')->delete($asset->image);
                }

                $path = $file->store('assets', 'public');
                Log::info('New image uploaded successfully', ['new_image_path' => $path]);
                $data['image'] = $path;
            } else {
                Log::error('Image file upload failed', ['error' => $file->getError()]);
                return back()->withErrors(['image' => 'Failed to upload image file.']);
            }
        } else {
            Log::info('No new image file provided', ['request_files' => $request->allFiles()]);
        }

        // Update asset
        $asset->update($data);
        Log::info('Asset basic information updated', ['updated_fields' => array_keys($data)]);

        // Update tags
        if ($request->has('tags')) {
            $asset->tags()->sync($request->tags);
            Log::info('Asset tags updated', ['new_tags' => $request->tags]);
        }

        // Update additional files with linked_count management
        if ($request->has('selected_files')) {
            // Get files that will be newly attached
            $newFileIds = array_diff($request->selected_files, $currentFileIds);

            // Get files that will be detached
            $detachedFileIds = array_diff($currentFileIds, $request->selected_files);

            // Increment linked_count for newly attached files
            if (!empty($newFileIds)) {
                AdditionalFile::whereIn('id', $newFileIds)->increment('linked_count');
                Log::info('Incremented linked_count for files', ['file_ids' => $newFileIds]);
            }

            // Decrement linked_count for detached files
            if (!empty($detachedFileIds)) {
                AdditionalFile::whereIn('id', $detachedFileIds)->decrement('linked_count');
                Log::info('Decremented linked_count for files', ['file_ids' => $detachedFileIds]);
            }

            // Perform the sync operation
            $asset->additionalFiles()->sync($request->selected_files);
            Log::info('Asset additional files updated', ['new_files' => $request->selected_files]);
        }

        // Handle removed files with linked_count update
        $removedFileIds = $request->input('remove_files', []);
        if (!empty($removedFileIds)) {
            foreach ($removedFileIds as $fileId) {
                $file = AdditionalFile::find($fileId);
                if ($file) {
                    // Delete the physical file
                    Storage::disk('public')->delete($file->file_path);
                    // Detach and delete the file record
                    $asset->additionalFiles()->detach($fileId);
                    $file->decrement('linked_count');
                    if ($file->linked_count < 0) {
                        $file->delete();
                    }
                    Log::info('File removed and linked_count updated', ['file_id' => $fileId]);
                }
            }
        }

        // Handle new additional files with linked_count
        if ($request->hasFile('additional_files')) {
            foreach ($request->file('additional_files') as $file) {
                $path = $file->store('additional-files', 'public');
                $additionalFile = AdditionalFile::create([
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'team_id' => $request->user()->currentTeam->id,
                    'linked_count' => 1  // Set initial linked_count
                ]);
                $asset->additionalFiles()->attach($additionalFile->id);
                Log::info('Additional file uploaded successfully with linked_count', ['path' => $path]);
            }
        }

        Log::info('Asset update completed successfully', ['asset_id' => $asset->id]);

        return redirect()->route('assets.show', $asset)->with('flash', [
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

<?php

namespace App\Http\Controllers;

use App\Models\AdditionalFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdditionalFileController extends Controller
{
    public static function create(array $data)
    {
        return AdditionalFile::create($data);
    }
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:4096',
            'description' => 'nullable|string',
            'team_id' => 'required|exists:teams,id'
        ]);

        $file = $request->file('file');
        $path = $file->store('additional-files', 'public');

        return AdditionalFile::create([
            'file_path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'description' => $request->description,
            'team_id' => $request->team_id
        ]);
    }

    public function destroy(AdditionalFile $additionalFile)
    {
        Storage::disk('public')->delete($additionalFile->file_path);
        $additionalFile->delete();

        return response()->noContent();
    }
}

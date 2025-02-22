<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamLogoController extends Controller
{
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'colored_logo' => ['nullable', 'image', 'max:1024'],
            'bw_logo' => ['nullable', 'image', 'max:1024'],
        ]);

        if ($request->hasFile('colored_logo')) {
            if ($team->colored_logo) {
                Storage::disk('public')->delete('team-logos/' . basename($team->colored_logo));
            }
            $coloredPath = $request->file('colored_logo')->store('team-logos', 'public');
            $team->colored_logo = Storage::url($coloredPath);
        }

        if ($request->hasFile('bw_logo')) {
            if ($team->bw_logo) {
                Storage::disk('public')->delete('team-logos/' . basename($team->bw_logo));
            }
            $bwPath = $request->file('bw_logo')->store('team-logos', 'public');
            $team->bw_logo = Storage::url($bwPath);
        }

        $team->save();

        return back(303);
    }
}
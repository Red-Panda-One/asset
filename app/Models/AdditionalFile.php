<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class AdditionalFile extends Model
{
    use HasUlids;

    protected $fillable = [
        'file_path',
        'name',
        'mime_type',
        'size',
        'description',
        'team_id'
    ];

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'asset_additional_file', 'additional_file_id', 'asset_id')
                    ->withTimestamps();
    }

    public function kits(): BelongsToMany
    {
        return $this->belongsToMany(Kit::class, 'kit_additional_file', 'additional_file_id', 'kit_id')
                    ->withTimestamps();
    }
}

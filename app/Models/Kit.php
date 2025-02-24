<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Kit extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'image',
        'team_id',
        'asset_count',
        'status'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'kit_asset', 'kit_id', 'asset_id');
    }
}

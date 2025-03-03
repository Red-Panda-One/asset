<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Asset extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'description',
        'image',
        'value',
        'team_id',
        'category_id',
        'location_id',
        'status',
        'custom_id'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'asset_tag', 'asset_id', 'tag_id');
    }

    public function kit(): BelongsTo
    {
        return $this->belongsTo(Kit::class);
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(AssetCustomFieldValue::class);
    }

    public function additionalFiles(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalFile::class, 'asset_additional_file', 'asset_id', 'additional_file_id')
                    ->using(AssetAdditionalFile::class)
                    ->withTimestamps();
    }
}

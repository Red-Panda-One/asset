<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class CustomField extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'type',
        'required',
        'active',
        'category_specific'
    ];

    protected $casts = [
        'required' => 'boolean',
        'active' => 'boolean',
        'category_specific' => 'boolean'
    ];

    public function options(): HasMany
    {
        return $this->hasMany(CustomFieldOption::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Categories::class, 'category_custom_field')
                    ->withTimestamps();
    }

    public function assetValues(): HasMany
    {
        return $this->hasMany(AssetCustomFieldValue::class);
    }

    public function kitValues(): HasMany
    {
        return $this->hasMany(KitCustomFieldValue::class);
    }
}
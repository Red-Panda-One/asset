<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class CustomFieldOption extends Model
{
    use HasUlids;

    protected $fillable = [
        'custom_field_id',
        'value',
        'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class KitCustomFieldValue extends Model
{
    use HasUlids;

    protected $fillable = [
        'kit_id',
        'custom_field_id',
        'value'
    ];

    public function kit(): BelongsTo
    {
        return $this->belongsTo(Kit::class);
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }
}
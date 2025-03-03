<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class AssetAdditionalFile extends Pivot
{
    use HasUlids;

    protected $table = 'asset_additional_file';
}

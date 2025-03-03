<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class KitAdditionalFile extends Pivot
{
    use HasUlids;

    protected $table = 'kit_additional_file';
}

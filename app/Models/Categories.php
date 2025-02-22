<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Categories extends Model
{
    use SoftDeletes, HasUlids;

    protected $fillable = [
        'name',
        'description',
        'color',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

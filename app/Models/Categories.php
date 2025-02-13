<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{

    use SoftDeletes;

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

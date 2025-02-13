<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'image',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

}

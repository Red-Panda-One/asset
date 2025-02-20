<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Location extends Model
{
    use HasFactory, HasUlids;

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

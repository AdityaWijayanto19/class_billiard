<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProTeam extends Model
{
    use HasFactory;

    protected $table = 'pro_teams';

    protected $fillable = [
        'name',
        'age',
        'origin',
        'address',
        'order',
        'is_active',
    ];
}

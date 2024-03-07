<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'token',
        'organs',
        'units',
        'sectors',
        'profile',
        'modules',
        'passchange',
        'status',
    ];

    protected $casts = [
        'organs' => 'array',
        'units' => 'array',
        'sectors' => 'array',
        'modules' => 'array',
    ];
}

<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use App\Casts\Json;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'nowlogin',
        'lastlogin'
    ];

    protected $casts = [
        'organs' => Json::class,
        'units' => Json::class,
        'sectors' => Json::class,
        'modules' => Json::class,
    ];

    public function nowlogin(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::utcToPtbr($value),
            set: fn (string $value) => Dates::ptbrToUtc($value)
        );
    }

    public function lastlogin(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::utcToPtbr($value),
            set: fn (string $value) => Dates::ptbrToUtc($value)
        );
    }
}

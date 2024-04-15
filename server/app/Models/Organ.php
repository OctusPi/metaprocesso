<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organ extends Model
{
    use HasFactory;

    protected $table = 'organs';

    protected $fillable = [
        'name',
        'cnpj',
        'phone',
        'email',
        'address',
        'postalcity',
        'postalcode',
        'status'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sectors';

    protected $fillable = [
        'name',
        'organ',
        'unit',
        'cnpj',
        'phone',
        'email',
        'address',
    ];

    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

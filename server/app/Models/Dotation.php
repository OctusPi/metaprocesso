<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dotation extends Model
{
    use HasFactory;

    protected $table = 'dotations';

    protected $fillable = [
        'name',
        'organ',
        'unit',
        'law',
        'description',
        'status',
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

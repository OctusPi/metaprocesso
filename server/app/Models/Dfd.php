<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dfd extends Model
{
    use HasFactory;

    protected $table = 'dfds';

    protected $fillable = [
        'cod',
        'date_ini',
        'category',
        'obj',
        'description',
        'organ',
        'unit',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockout extends Model
{
    use HasFactory;

    protected $table = 'stockouts';

    protected $fillable = [
        'date_ini',
        'cod',
        'organ',
        'unit',
        'sector',
        'description',
        'issuer',
        'requester',
    ];

    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'organ_id',
        'unit_id',
    ];

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class, 'organ_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}

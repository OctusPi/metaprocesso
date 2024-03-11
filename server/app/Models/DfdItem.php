<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DfdItem extends Model
{
    use HasFactory;

    protected $table = 'dfditems';

    protected $fillable = [
        'dfd_id',
        'item_id',
        'quantity',
        'program_id',
        'dotation_id',
    ];

    public function dfd(): BelongsTo
    {
        return $this->belongsTo(Dfd::class, 'dfd_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function dotation(): BelongsTo
    {
        return $this->belongsTo(Dotation::class, 'dotation_id');
    }
}

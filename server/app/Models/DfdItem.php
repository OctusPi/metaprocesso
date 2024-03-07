<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DfdItem extends Model
{
    use HasFactory;

    protected $table = 'dfditems';

    protected $fillable = [
        'dfd',
        'item',
        'quantity',
        'program',
        'dotation',
    ];

    public function dfd()
    {
        return $this->belongsTo(Dfd::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function dotation()
    {
        return $this->belongsTo(Dotation::class);
    }
}

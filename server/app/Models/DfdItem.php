<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function dfd():HasOne
    {
        return $this->hasOne(Dfd::class, 'id');
    }

    public function item():HasOne
    {
        return $this->hasOne(CatalogItem::class, 'id');
    }

    public function program():HasOne
    {
        return $this->hasOne(Program::class, 'id');
    }

    public function dotation():HasOne
    {
        return $this->hasOne(Dotation::class, 'id');
    }
}

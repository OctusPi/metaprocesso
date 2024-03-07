<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceRegistrationDoc extends Model
{
    use HasFactory;

    protected $table = 'priceregistrationdocs';

    protected $fillable = [
        'cod',
        'category',
        'obj',
        'description',
        'organ',
        'unit',
        'date_ini',
        'date_fin',
        'estimated_value',
        'approved_value',
        'supplier',
        'additive',
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

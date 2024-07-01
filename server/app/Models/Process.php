<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Process extends Model
{
    use HasFactory;

    protected $table = 'processes';

    protected $fillable = [
        'protocol',
        'ip',
        'date_ini',
        'hour_ini',
        'year_pca',
        'type',
        'modality',
        'organ',
        'units',
        'ordinators',
        'comission',
        'comission_members',
        'comission_address',
        'author',
        'object',
        'situation',
        'initial_value',
        'winner_value',
        'dfds',
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author');
    }
}

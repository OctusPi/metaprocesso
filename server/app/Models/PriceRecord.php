<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceRecord extends Model
{
    use HasFactory;

    public string $table = 'price_records';

    protected $fillable = [
        'protocol',
        'ip',
        'date_ini',
        'date_fin',
        'process',
        'organ',
        'units',
        'comission',
        'comission_members',
        'suppliers',
        'author',
        'status'
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function process():HasOne
    {
        return $this->hasOne(Process::class, 'id', 'process');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author');
    }

    public function proposal():BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public static function list_status():array
    {
        return [
            ['id' => 1, 'title' => 'Iniciada'],
            ['id' => 2, 'title' => 'Pendente'],
            ['id' => 3, 'title' => 'Finalizada']
        ];
    }
}

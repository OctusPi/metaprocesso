<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Proposal extends Model
{
    use HasFactory;

    public string $table = 'price_records_proposals';

    protected $fillable = [
        'protocol',
        'ip',
        'date_ini',
        'hour_ini',
        'organ',
        'process',
        'price_record',
        'modality',
        'supplier',
        'items',
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

    public function pricerecord():HasOne
    {
        return $this->hasOne(PriceRecord::class, 'id', 'price_record');
    }

    public static function list_modalitys():array
    {
        return [
            ['id' => 1, 'title' => 'Solicitação por E-mail'],
            ['id' => 2, 'title' => 'Inserção Manual'],
            ['id' => 3, 'title' => 'Sites de Varejo'],
            ['id' => 4, 'title' => 'Banco de Preços Gov'],
        ];
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

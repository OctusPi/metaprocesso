<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceRecordProposal extends Model
{
    use HasFactory;

    protected $table = 'price_records_proposals';

    protected $fillable = [
        'id',
        'protocol',
        'ip',
        'token',
        'date_ini',
        'hour_ini',
        'date_fin',
        'hour_fin',
        'organ',
        'process',
        'price_record',
        'modality',
        'supplier',
        'items',
        'status'
    ];


}

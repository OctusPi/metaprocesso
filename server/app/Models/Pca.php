<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pca extends Model
{
    use HasFactory;

    protected $table = 'pcas';

    protected $fillable = [
        'organ_id',
        'comission_id',
        'author_id',
        'reference_year',
        'emission',
        'price',
        'comission_members',
        'observations',
        'status',
    ];

    const S_ELABORATION = 1;
    const S_APROVED = 2;
    const S_DENIED = 3;

    public static function list_status(): array
    {
        return [
            self::S_ELABORATION => 'Em Elaboração',
            self::S_APROVED => 'Aprovado',
            self::S_DENIED => 'Recusado',
        ];
    }
}

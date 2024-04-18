<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'orgao_id',
        'unit_id',
        'cod',
        'name',
        'description',
        'und',
        'volume',
        'origin',
        'type',
        'category',
        'status'
    ];

    public static function list_origem(): array
    {
        return [
            1 => 'Catálogo Federal',
            2 => 'Catalogo Local'
        ];
    }

    public static function list_tipo(): array
    {
        return [
            1 => 'Material',
            2 => 'Serviço'
        ];
    }

    public static function list_categoria(?int $key = null): string|array
    {
        $data = [
            1 => 'Material',
            2 => 'Serviço',
            3 => 'Obras',
            4 => 'Serviços de Engenharia',
            5 => 'Soluções de TIC',
            6 => 'Locação de Imóveis',
            7 => 'Alienação/Concessão/Permissão',
            8 => 'Obras e Serviços de Engenharia',
        ];

        if (!is_null($key)) {
            return $data[$key] ?? '';
        }

        return $data;
    }

    public static function list_status(): array
    {
        return [
            1 => 'Em avaliação',
            2 => 'Deferido',
            3 => 'Indeferido'
        ];
    }
}

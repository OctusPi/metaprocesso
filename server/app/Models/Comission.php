<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comission extends Model
{
    use HasFactory;

    protected $table = 'comissions';

    protected $fillable = [
        'organ_id',
        'unit_id',
        'name',
        'type',
        'document',
        'description',
        'start_term',
        'end_term',
        'status'
    ];

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class, 'organ_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function startTerm(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public function endTerm(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public static function validateFields(?int $id = null):array
    {
        return [
            'organ_id'   => 'required',
            'unit_id'    => 'required',
            'name'       => 'required',
            'type'       => 'required',  
            'start_term' => 'required',
            'status'     => 'required',
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique'   => 'Ordenador já registrado no sistema!'
        ];
    }

    public static function list_types(): array
    {
        return [
            ['id'=>0,'title'=> 'Comissão de Planejamento e Contratação'],
            ['id'=>1,'title'=> 'Comissão de Licitação'],
            ['id'=>2,'title'=> 'Comissão de Gestão e Fiscalização de Contratos'],
            ['id'=>3,'title'=> 'Comissão de Auditoria de Processo de Contratação']
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id'=>0,'title'=> 'Inativa'],
            ['id'=>1,'title'=> 'Ativa'],
            ['id'=>2,'title'=> 'Suspensa'],
            ['id'=>3,'title'=> 'Finalizada']
        ];
    }
}

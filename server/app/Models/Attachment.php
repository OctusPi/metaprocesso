<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    public const ETP = User::MOD_ETPS['id'];
    public const PROCESS = User::MOD_PROCESSES['id'];

    protected $table = 'attachments';
    protected $fillable = [
        'id',
        'organ_id',
        'origin',
        'protocol',
        'type',
        'name',
        'document',
    ];

    public function rules(): array
    {
        return [
            'origin' => 'required',
            'protocol' => 'required',
            'type' => 'required',
            'document' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
        ];
    }

    public static function list_types():array
    {
        return [
            ['id' => 1, 'title' => 'Aviso de Contratação Direta'],
            ['id' => 2, 'title' => 'Edital'],
            ['id' => 3, 'title' => 'Minuta do Contrato'],
            ['id' => 4, 'title' => 'Termo de Referência'],
            ['id' => 5, 'title' => 'Anteprojeto'],
            ['id' => 6, 'title' => ' Projeto Básico'],
            ['id' => 7, 'title' => 'Estudo Técnico Preliminar'],
            ['id' => 8, 'title' => 'Projeto Executivo'],
            ['id' => 9, 'title' => 'Mapa de Riscos'],
            ['id' => 10, 'title' => 'DFD'],
            ['id' => 11, 'title' => 'Ata de Registro de Preço'],
            ['id' => 12, 'title' => 'Contrato'],
            ['id' => 13, 'title' => 'Termo de Rescisão'],
            ['id' => 14, 'title' => 'Termo Aditivo'],
            ['id' => 15, 'title' => 'Termo de Apostilamento'],
            ['id' => 16, 'title' => 'Outros'],
            ['id' => 17, 'title' => 'Nota de Empenho'],
            ['id' => 18, 'title' => 'Relatório Final de Contrato']
        ];
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::TZ, Dates::PTBR),
        );
    }


    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }
}

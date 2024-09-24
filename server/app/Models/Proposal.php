<?php

namespace App\Models;

use App\Casts\Json;
use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proposal extends Model
{
    use HasFactory;

    const S_WAITING = 0;
    const S_START = 1;
    const S_OPENED = 2;
    const S_PENDING = 3;
    const S_FINISHED = 4;

    const M_MAIL = 1;
    const M_MANUAL = 2;
    const M_SITES = 3;
    const M_BANK = 4;

    protected $table = 'price_records_proposals';

    protected $fillable = [
        'id',
        'protocol',
        'ip',
        'author',
        'token',
        'date_ini',
        'hour_ini',
        'date_fin',
        'hour_fin',
        'organ',
        'process',
        'price_record',
        'supplier',
        'modality',
        'items',
        'logomarca',
        'document',
        'representation',
        'cpf',
        'status'
    ];

    protected $casts = [
        'items' => Json::class
    ];

    public function rules(): array
    {
        return [
            'protocol' => 'required',
            'ip' => 'required',
            'author' => 'required',
            'token' => 'required',
            'date_ini' => 'required',
            'hour_ini' => 'required',
            'organ' => 'required',
            'process' => 'required',
            'price_record' => 'required',
            'supplier' => [
                'requried',
                Rule::unique('price_records_proposals', 'supplier')->where(function ($query) {
                    return $query->where('supplier', '!=', null)->where('price_record', $this->price_record);
                })->ignore($this->id)
            ],
            'modality' => 'required',
            'status' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'Já existe uma coleta para esse fornecedor.'
        ];
    }

    public function dateIni(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public function dateFin(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public static function list_modalitys(): array
    {
        return [
            ['id' => self::M_MAIL, 'title' => 'Solicitação por E-mail'],
            ['id' => self::M_MANUAL, 'title' => 'Inserção Manual'],
            ['id' => self::M_SITES, 'title' => 'Sites de Varejo'],
            ['id' => self::M_BANK, 'title' => 'Banco de Preços Gov'],
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => self::S_WAITING, 'title' => 'A Enviar'],
            ['id' => self::S_START, 'title' => 'Enviada'],
            ['id' => self::S_OPENED, 'title' => 'Aberta'],
            ['id' => self::S_PENDING, 'title' => 'Pendente'],
            ['id' => self::S_FINISHED, 'title' => 'Finalizada']
        ];
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function process(): HasOne
    {
        return $this->hasOne(Process::class, 'id', 'process');
    }

    public function price_record(): HasOne
    {
        return $this->hasOne(PriceRecord::class, 'id', 'price_record');
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier');
    }

}

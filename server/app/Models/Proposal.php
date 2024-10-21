<?php

namespace App\Models;

use App\Casts\Json;
use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'author_id',
        'token',
        'date_ini',
        'hour_ini',
        'date_fin',
        'hour_fin',
        'organ_id',
        'process_id',
        'pricerecord_id',
        'supplier_id',
        'modality',
        'items',
        'logomarca',
        'document',
        'representation',
        'cpf',
        'status',
        'global'
    ];

    protected $casts = [
        'items' => Json::class
    ];

    public function rules(): array
    {
        return [
            'protocol' => 'required',
            'ip' => 'required',
            'author_id' => 'required',
            'token' => 'required',
            'date_ini' => 'required',
            'hour_ini' => 'required',
            'organ_id' => 'required',
            'process_id' => 'required',
            'pricerecord_id' => 'required',
            'supplier_id' => [
                'requried',
                Rule::unique('price_records_proposals', 'supplier_id')->where(function ($query) {
                    return $query->where('supplier', '!=', null)->where('pricerecord_id', $this->pricerecord_id);
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

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function pricerecord(): BelongsTo
    {
        return $this->belongsTo(PriceRecord::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

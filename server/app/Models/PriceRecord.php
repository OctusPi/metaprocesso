<?php

namespace App\Models;

use App\Casts\Json;
use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceRecord extends Model
{
    use HasFactory;

    const S_START = 1;
    const S_PENDING = 2;
    const S_FINISHED = 3;

    protected $table = 'price_records';

    protected $fillable = [
        'id',
        'protocol',
        'ip',
        'date_ini',
        'date_fin',
        'process_id',
        'organ_id',
        'comission_id',
        'comission_members',
        'suppliers',
        'suppliers_justification',
        'author_id',
        'status'
    ];

    protected $casts = [
        'comission_members' => Json::class,
        'suppliers' => Json::class
    ];

    public function rules():array
    {
        return [
            'protocol'  => 'required',
            'ip'        => 'required',
            'date_ini'  => 'required',
            'date_fin'  => 'required',
            'process_id'   => ['required', Rule::unique('price_records', 'process_id')->ignore($this->id)],
            'organ_id'     => 'required',
            'comission_id' => 'required',
            'author_id'    => 'required',
            'status'    => 'required',
        ];
    }

    public function messages():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique'   => 'Registro de preço já iniciada para o processo...'
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

    public static function list_status():array
    {
        return [
            ['id' => self::S_START, 'title' => 'Iniciada'],
            ['id' => self::S_PENDING, 'title' => 'Pendente'],
            ['id' => self::S_FINISHED, 'title' => 'Finalizada']
        ];
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }
    
    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}

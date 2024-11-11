<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

class Pca extends Model
{
    use HasFactory;

    const S_ELABORATION = 1;
    const S_APROVED = 2;
    const S_DENIED = 3;
    const S_PUBLISHED = 4;

    protected $table = 'pcas';

    protected $fillable = [
        'organ_id',
        'comission_id',
        'author_id',
        'reference_year',
        'emission',
        'protocol',
        'price',
        'comission_members',
        'observations',
        'status',
    ];

    public function emission(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::TZ, Dates::PTBR),
        );
    }

    public function rules(): array
    {
        return [
            'reference_year' => [
                'required',
                Rule::unique('pcas', 'protocol')
                    ->where('organ_id', $this->organ_id)
                    ->ignore($this->id)
            ],
            'protocol' => 'required',
            'emission' => 'required',
            'organ_id' => 'required',
            'comission_id' => 'required',
            'author_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'PCA já registrado para o ano de referência!'
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => self::S_ELABORATION, 'title' => 'Em Elaboração'],
            ['id' => self::S_APROVED, 'title' => 'Aprovado'],
            ['id' => self::S_DENIED, 'title' => 'Revogado'],
            ['id' => self::S_PUBLISHED, 'title' => 'Publicado PNCP'],
        ];
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
}

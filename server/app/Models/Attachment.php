<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    public const ETP = 1;

    protected $table = 'attachments';
    protected $fillable = [
        'id',
        'organ',
        'origin',
        'protocol',
        'type',
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

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::TZ, Dates::PTBR),
        );
    }
}

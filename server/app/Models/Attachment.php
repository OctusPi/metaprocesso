<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\Dates;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';
    protected $fillable = [
        'origin',
        'protocol',
        'type',
        'file',
    ];

    public static function validateFields(?int $id = null): array
    {
        return [
            'origin' => 'required',
            'protocol' => 'required',
            'type' => 'required',
            'file' => 'required',
        ];
    }

    public static function validateMsg(): array
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

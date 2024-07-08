<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    //STATUS VALUE
    public const S_INACTIVE = 0;
    public const S_ACTIVE = 1;
    public const S_SUSPENDED = 2;
    public const S_ANALYSIS = 3;

    public static function validateFields(?int $id = null): array
    {
        return [];
    }

    public static function validateMsg():array
    {
        return [];
    }

}

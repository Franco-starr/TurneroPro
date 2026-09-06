<?php

namespace App\Models;

use Database\Factories\StoreSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    /** @use HasFactory<StoreSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'opening_time',
        'closing_time',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'price' => 'decimal:2',
        ];
    }
}

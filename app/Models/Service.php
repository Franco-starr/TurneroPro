<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'price',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'price' => 'decimal:2',
        ];
    }
}

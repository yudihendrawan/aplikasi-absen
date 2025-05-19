<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'address', 'phone', 'latitude', 'longitude'];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function presents(): HasMany
    {
        return $this->hasMany(Present::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Present extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'store_id',
        'longitude',
        'latitude',
        'status',
        'date',
        'device_info',
        'check_in_ip',
        'check_out_ip',
        'check_in_time',
        'check_out_time',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}

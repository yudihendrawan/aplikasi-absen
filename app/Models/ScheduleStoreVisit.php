<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScheduleStoreVisit extends Model
{

    protected $fillable = [
        'id',
        'schedule_id',
        'store_id',
        'expected_invoice_amount',
        'checkin_time',
        'checkout_time'
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}

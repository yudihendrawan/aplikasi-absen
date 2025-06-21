<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visit_date',
        'notes',
        'created_by',
        'time_tolerance',
    ];

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function storeVisits(): HasMany
    {
        return $this->hasMany(ScheduleStoreVisit::class);
    }
}

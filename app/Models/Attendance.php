<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Attendance extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'id',
        'schedule_store_visit_id',
        'attended_at',
        'actual_invoice_amount',
        'note',
        'latitude',
        'longitude',
        'device_info',
        'check_in_ip',
        'check_out_ip',
        'check_in_time',
        'check_out_time',
    ];

    public function storeVisit(): BelongsTo
    {
        return $this->belongsTo(ScheduleStoreVisit::class, 'schedule_store_visit_id');
    }
}

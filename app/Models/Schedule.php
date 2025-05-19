<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'presents_id',
        'created_by',
        'date',
        'check_in',
        'check_out',
        'time_tolerance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function present()
    {
        return $this->belongsTo(Present::class, 'presents_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

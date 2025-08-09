<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'reason', 'description', 'start_date', 'end_date', 'approved_at', 'approved_by', 'rejected_at', 'rejected_by', 'rejection_reason'];

    // protected $cast = [
    //     'start_date' => 'datetime',
    //     'end_date' => 'datetime',
    // ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}

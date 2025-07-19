<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function schedules(): HasMany
    {

        return $this->hasMany(Schedule::class);
    }

    public function createdSchedules(): HasMany
    {
        // untuk user yang punya role admin (role permission menggunakan spatie/laravel-permission)
        return $this->hasMany(Schedule::class, 'created_by');
    }

    // public function attendances(): HasMany
    // {
    //     // untuk user yang punya role sales (role permission menggunakan spatie/laravel-permission)
    //     return $this->hasMany(Attendance::class);
    // }

    // Di User.php
    public function attendances()
    {
        return $this->hasManyThrough(
            Attendance::class,
            ScheduleStoreVisit::class,
            'user_id', // foreign key di schedule_store_visits
            'schedule_store_visit_id', // foreign key di attendances
            'id', // local key di users
            'id'  // local key di schedule_store_visits
        );
    }


    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'wilayah_id',
        'ranting_id',
        'name',
        'email',
        'password',
        'role',
        'no_telp'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function ranting(): BelongsTo
    {
        return $this->belongsTo(DataRanting::class, 'ranting_id');
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    public function isPc(): bool
    {
        return $this->role === 'pc';
    }

    public function isMwc(): bool
    {
        return $this->role === 'mwc';
    }

    public function isRanting(): bool
    {
        return $this->role === 'ranting';
    }

    public function KoinNuTransactions(): HasMany
    {
        return $this->hasMany(KoinNuTransaction::class);
    }

    public function KoinNuDistributions(): HasMany
    {
        return $this->hasMany(KoinNuDistribution::class);
    }

    public function InfaqMwcTransaction(): HasMany
    {
        return $this->hasMany(InfaqMwcTransaction::class);
    }

    public function InfaqMwcDistribution(): HasMany
    {
        return $this->hasMany(InfaqMwcDistribution::class);
    }

    const EXCEPTION_ROLES = ['developer'];


}

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

    public function koin_nu_transactions(): HasMany
    {
        return $this->hasMany(koin_nu_transaction::class);
    }

    public function koin_nu_distributions(): HasMany
    {
        return $this->hasMany(koin_nu_distribution::class);
    }

    public function infaq_mwc_transactions(): HasMany
    {
        return $this->hasMany(infaq_mwc_transactions::class);
    }

    public function infaq_mwc_distributions(): HasMany
    {
        return $this->hasMany(infaq_mwc_distributions::class);
    }


}

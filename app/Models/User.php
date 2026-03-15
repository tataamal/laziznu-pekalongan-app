<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telpon',
        'wilayah_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
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

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }
}

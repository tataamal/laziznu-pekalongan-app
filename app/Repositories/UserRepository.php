<?php

namespace App\Repositories;

use App\Models\DataRanting;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getUsers(
        ?string $role = null,
        ?int $wilayahId = null,
        ?int $rantingId = null
    ): Collection {
        return User::query()
            ->whereNotIn('role', User::EXCEPTION_ROLES)
            ->when($role, fn($q) => $q->where('role', $role))
            ->when($wilayahId, fn($q) => $q->where('wilayah_id', $wilayahId))
            ->when($rantingId, fn($q) => $q->where('ranting_id', $rantingId))
            ->orderBy('name')
            ->get();
    }

    // Hitung jumlah user berdasarkan role
    public function countByRole(string $role): int
    {
        return User::where('role', $role)->count();
    }

    // Ambil semua data wilayah
    public function getAllWilayah(): Collection
    {
        return Wilayah::orderBy('name')->get();
    }

    // Ambil semua data ranting
    public function getAllRanting(?int $wilayahId = null): Collection
    {
        return DataRanting::query()
            ->when($wilayahId, fn($q) => $q->where('wilayah_id', $wilayahId))
            ->orderBy('name')
            ->get();
    }
}
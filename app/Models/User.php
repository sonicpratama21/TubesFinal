<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Pengganti UserModel (tabel `users`).
 * Kolom: nama_lengkap, username, email, password, role (admin|pengguna).
 */
#[Fillable(['nama_lengkap', 'username', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    const UPDATED_AT = null;

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function reservasi()
    {
        return $this->hasMany(Reservasi::class);
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is pemilik
     */
    public function isPemilik(): bool
    {
        return $this->role === 'pemilik';
    }

    /**
     * Check if user can access resource
     */
    public function canAccessResource(string $resource): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Pemilik hanya bisa akses laporan
        $allowedForPemilik = [
            'JurnalResource',
            'BukuBesarResource',
            'NeracaPage',
        ];

        return in_array($resource, $allowedForPemilik);
    }
}

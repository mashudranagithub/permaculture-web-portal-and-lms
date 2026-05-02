<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasOrganization;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasOrganization;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_approved',
        'avatar',
        'organization_id',
    ];

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrl(): string
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar) 
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=198754&color=fff';
    }

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

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Check if user has a specific role.
     * Usage: $user->hasRole('admin') or $user->hasRole(['admin', 'super-admin'])
     */
    public function hasRole($role): bool
    {
        if (is_array($role)) {
            return $this->roles->pluck('slug')->intersect($role)->isNotEmpty();
        }

        return $this->roles->contains('slug', $role);
    }

    /**
     * Check if user has a specific permission.
     * Checks both direct permissions and role-based permissions.
     */
    public function hasPermission($permission): bool
    {
        // 1. Super Admin Bypass (Master Key)
        if ($this->hasRole('super-admin')) {
            return true;
        }

        // 2. Check direct permissions
        if ($this->permissions->contains('slug', $permission)) {
            return true;
        }

        // 3. Check role-based permissions
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permission)) {
                return true;
            }
        }

        return false;
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** Check whether the user's organization is approved and active */
    public function hasApprovedOrganization(): bool
    {
        if (!$this->organization_id) return true; // LMS admins have no org
        return $this->organization?->isActive() ?? false;
    }
}

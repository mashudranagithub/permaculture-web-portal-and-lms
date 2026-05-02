<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo',
        'website',
        'description',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'settings',
    ];

    protected $appends = [
        'logo_url',
        'status_badge',
    ];

    protected $casts = [
        'settings'    => 'array',
        'approved_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /** The LMS admin who approved this org */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── Status Helpers ───────────────────────────────────────────────────────

    public function isActive(): bool   { return $this->status === 'active'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isSuspended(): bool { return $this->status === 'suspended'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    // ─── Boot ─────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        // Auto-generate slug from name on creation
        static::creating(function (Organization $org) {
            if (empty($org->slug)) {
                $org->slug = Str::slug($org->name);
            }
        });
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=198754&color=fff&size=128';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'active'    => ['label' => 'Active',    'class' => 'success'],
            'pending'   => ['label' => 'Pending',   'class' => 'warning'],
            'suspended' => ['label' => 'Suspended', 'class' => 'danger'],
            'rejected'  => ['label' => 'Rejected',  'class' => 'secondary'],
            default     => ['label' => 'Unknown',   'class' => 'light'],
        };
    }
}

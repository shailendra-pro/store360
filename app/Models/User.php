<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'business_name',
        'email',
        'contact_email',
        'username',
        'password',
        'role',
        'secure_link',
        'secure_link_expires_at',
        'custom_hours',
        'company',
        'logo_path',
        'is_active',
        'expires_at',
        'business_description',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
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
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'secure_link_expires_at' => 'datetime',
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
     * Check if user is business
     */
    public function isBusiness(): bool
    {
        return $this->role === 'business';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if business account is active
     */
    public function isBusinessActive(): bool
    {
        if (!$this->isBusiness()) {
            return false;
        }

        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if secure link is valid
     */
    public function isSecureLinkValid(): bool
    {
        if (!$this->secure_link) {
            return false;
        }

        if (!$this->secure_link_expires_at) {
            return false;
        }

        return $this->secure_link_expires_at->isFuture();
    }

    /**
     * Generate secure link
     */
    public function generateSecureLink(int $hours = 24): string
    {
        $this->secure_link = Str::random(64);
        $this->secure_link_expires_at = now()->addHours($hours);
        $this->custom_hours = $hours;
        $this->save();

        return $this->secure_link;
    }

    /**
     * Extend secure link expiration
     */
    public function extendSecureLink(int $additionalHours): bool
    {
        if (!$this->secure_link) {
            return false;
        }

        $this->secure_link_expires_at = $this->secure_link_expires_at->addHours($additionalHours);
        $this->custom_hours += $additionalHours;
        $this->save();

        return true;
    }

    /**
     * Get secure link URL
     */
    public function getSecureLinkUrlAttribute(): string
    {
        if (!$this->secure_link) {
            return '';
        }

        return url("/secure/{$this->secure_link}");
    }

    /**
     * Get business logo URL
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return Storage::disk('public')->url($this->logo_path);
        }

        return asset('assets/images/default-logo.png');
    }

    /**
     * Scope for active businesses
     */
    public function scopeActiveBusinesses($query)
    {
        return $query->where('role', 'business')
                    ->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope for expired businesses
     */
    public function scopeExpiredBusinesses($query)
    {
        return $query->where('role', 'business')
                    ->where('expires_at', '<', now());
    }

    /**
     * Scope for users with valid secure links
     */
    public function scopeWithValidSecureLinks($query)
    {
        return $query->whereNotNull('secure_link')
                    ->where('secure_link_expires_at', '>', now());
    }

    /**
     * Scope for users by company
     */
    public function scopeByCompany($query, $company)
    {
        return $query->where('company', 'like', "%{$company}%");
    }

    /**
     * Scope for users by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status === 'active') {
            return $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            return $query->where('is_active', false);
        }
        return $query;
    }

    /**
     * Get display name (business name or regular name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->business_name ?: $this->name;
    }

    /**
     * Get primary email (contact email or regular email)
     */
    public function getPrimaryEmailAttribute(): string
    {
        return $this->contact_email ?: $this->email;
    }

    /**
     * Get remaining hours for secure link
     */
    public function getRemainingHoursAttribute(): int
    {
        if (!$this->secure_link_expires_at) {
            return 0;
        }

        return max(0, now()->diffInHours($this->secure_link_expires_at, false));
    }
}

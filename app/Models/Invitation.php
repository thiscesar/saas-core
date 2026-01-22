<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Invitation extends BaseModel
{
    /** @use HasFactory<\Database\Factories\InvitationFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'pin',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'  => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * Check if the invitation is expired.
     */
    public function isExpired(): bool
    {
        /** @var \Illuminate\Support\Carbon $expiresAt */
        $expiresAt = $this->expires_at;

        return $expiresAt->isPast();
    }

    /**
     * Check if the invitation has been accepted.
     */
    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /**
     * Check if the invitation is valid (not expired and not accepted).
     */
    public function isValid(): bool
    {
        return ! $this->isExpired() && ! $this->isAccepted();
    }

    /**
     * Mark the invitation as accepted.
     */
    public function markAsAccepted(): void
    {
        $this->update(['accepted_at' => now()]);
    }

    /**
     * Generate a unique token for the invitation.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Generate a 6-digit PIN code.
     */
    public static function generatePin(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

<?php

declare(strict_types = 1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use AuditingAuditable;
    use SoftDeletes;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function logins(): HasMany
    {
        return $this->hasMany(Login::class);
    }

    public function invitation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /**
     * Get user initials for avatar placeholder.
     */
    public function getInitials(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Get user avatar URL.
     */
    public function getAvatarUrl(): ?string
    {
        return $this->getAttributeValue('avatar_url');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'slack_id',
        'slack_access_token',
        'slack_refresh_token',
        'avatar_url',
        'status',
        'invitation_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'slack_access_token',
        'slack_refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'password_set_at'     => 'datetime',
            'is_admin'            => 'boolean',
            'slack_access_token'  => 'encrypted',
            'slack_refresh_token' => 'encrypted',
        ];
    }
}

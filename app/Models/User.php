<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'surname',
        'phone',
        'email',
        'password',
        'workos_id',
        'workos_connection_id',
        'workos_connection_type',
        'workos_organization_id',
        'workos_directory_user_id',
        'workos_raw_attributes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'workos_raw_attributes',
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
            'workos_raw_attributes' => 'array',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Check if user is authenticated via WorkOS
     */
    public function isWorkOsUser(): bool
    {
        return !empty($this->workos_id);
    }

    /**
     * Get the user's organization from WorkOS
     */
    public function getWorkOsOrganization()
    {
        if (!$this->workos_organization_id) {
            return null;
        }

        $workos = app(\App\Services\WorkOsService::class);
        return $workos->getOrganization($this->workos_organization_id);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Class User
 *
 * @property string $id
 * @property string $department_id
 * @property string $group_id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $phone
 * @property string $role
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'sso_mysql';

    protected $fillable = [
        'department_id',
        'group_id',
        'name',
        'username',
        'password',
        'phone',
        'role',
        'type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is internal (karyawan)
     */
    public function isInternal(): bool
    {
        return $this->type === 'internal';
    }

    /**
     * Check if user is partner (mitra)
     */
    public function isPartner(): bool
    {
        return $this->type === 'partner';
    }

    /**
     * Get organization name based on user type
     * Internal -> Department name
     * Partner -> Group/Partner name
     */
    public function getOrganizationNameAttribute(): string
    {
        if ($this->isInternal()) {
            return $this->department?->name ?? 'Internal';
        }
        
        // Partner: get partner name via group
        return $this->group?->partner?->name ?? $this->group?->name ?? 'Mitra';
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'password'   => 'hashed',
        ];
    }
}

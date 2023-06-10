<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property string $email
 * @property int $email_verified_at
 * @property string $password
 * @property string $timezone
 * @property string $remember_token
 * @property int $created_at
 * @property int $updated_at
 *
 * @mixin \Illuminate\Database\Query\Builder
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at',
        'remember_token',
        'company_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get panel company
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyModel::class, 'company_id', 'id');
    }
}

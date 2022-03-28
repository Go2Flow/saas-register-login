<?php
namespace Go2Flow\SaasRegisterLogin\Models;

use Go2Flow\SaasRegisterLogin\Database\Factories\UserFactory;
use Go2Flow\SaasRegisterLogin\Models\AbstractModels\AbstractUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends AbstractUser implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['name'];

    /** @return UserFactory */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function getNameAttribute()
    {
        return $this->firstname.' '.$this->lastname;
    }
}

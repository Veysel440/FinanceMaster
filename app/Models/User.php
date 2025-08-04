<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $profile_photo
 * @property string $currency
 * @property string $locale
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'profile_photo', 'currency', 'locale',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo
            ? url('storage/' . $this->profile_photo)
            : null;
    }

    public function transactions() { return $this->hasMany(Transaction::class); }
    public function categories()    { return $this->hasMany(Category::class); }
    public function budgets()       { return $this->hasMany(Budget::class); }
    public function goals()         { return $this->hasMany(Goal::class); }


    public function setPasswordAttribute($value)
    {
        if (\Illuminate\Support\Str::startsWith($value, '$2y$')) {
            $this->attributes['password'] = $value;
        } else {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}

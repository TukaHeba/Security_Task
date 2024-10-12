<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Set the password attribute after hashing it.
     *
     * @param string $value 
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Define the many-to-many relationship between users and roles.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    /**
     * Check if the user has a specific role by its name.
     * 
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Assign multiple roles to the user by role's IDs.
     *
     * @param array $roleIds
     * @return void
     */
    public function assignRoles(array $roleIds)
    {
        $this->roles()->syncWithoutDetaching($roleIds);
    }

    /**
     * Remove a role from the user.
     * 
     * @param string $roleName
     * @return void
     */
    public function removeRole(string $roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $this->roles()->detach($role->id);
    }

    /**
     * Check if the user has a specific permission.
     * 
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(string $permissionName)
    {
        return $this->roles()->whereHas('permissions', function ($q) use ($permissionName) {
            $q->where('name', $permissionName);
        })->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Define the many-to-many relationship between roles and users.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')->withTimestamps();
    }

    /**
     * Define the many-to-many relationship between roles and permissions.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }

    /**
     * Assign a permission or more to a role by permission name.
     * 
     * @param array $permissionNames
     * @return void
     */
    public function assignPermission(array $permissionNames)
    {
        $permissions = Permission::whereIn('name', $permissionNames)->get();
        $this->permissions()->syncWithoutDetaching($permissions->pluck('id')->toArray());
    }

    /**
     * Assign multiple permissions to the role by permission's IDs.

     * @param array $permissionIds
     * @return void
     */
    public function assignPermissions(array $permissionIds)
    {
        $this->permissions()->syncWithoutDetaching($permissionIds);
    }

    /**
     * Revoke a specific permission from a role.
     * 
     * @param string $permissionName
     * @return void
     */
    public function removePermission($permissionName)
    {
        $permission = Permission::where('name', $permissionName)->firstOrFail();
        $this->permissions()->detach($permission->id);
    }

    /** 
     * Check if the role has a certain permission.
     * 
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission($permissionName)
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }
}

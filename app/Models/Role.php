<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
use  App\Models\Access\Permission;

class Role extends Model
{
    use HasFactory,SearchableTrait;


    protected $searchable = [
        'columns' => [
            'roles.name' => 20,
            'roles.slug'=> 20,
        ],
    ];

    /**
     * The attributes that can be used for sorting with query string filtering.
     *
     * @var array
     */
    public $sortable = [
        'id', 'name', 'created_at', 'updated_at',
    ];

    protected $fillable = [
        'slug',
        'name',
        'description',
        'all',
        'locked',
        'abbreviation',
        'created_by',
    ];


    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'users', 'permissions'
    ];

    /**
     * The users associated with the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')->withTrashed();
    }

   

    /**
     * The permissions associated with the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

}

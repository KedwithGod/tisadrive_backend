<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Access\Role;
use Illuminate\Http\Request;
use App\Models\Access\Permission;

class PermissionController extends Controller
{
    // public function index($id)
    // {
    //     $role = Role::find($id);
    //     $permissions = Permission::all();
    //     $permissionRole = $role->permissions()->get();

    //     return Inertia::render('pages/roles/permission', [
    //         'role' => $role,
    //         'permissions' => $permissions,
    //         'permissionRole' => $permissionRole,
    //     ]);
    // }

    public function index($id)
{
    $role = Role::find($id);
    $currentUserRole = auth()->user()->roles()->first();

    if ($currentUserRole->slug === 'super-admin') {
        // Super admin can see all permissions
        $permissions = Permission::all();
    } else {
        // Admins only see their assigned permissions
        $allowedPermissionSlugs = $currentUserRole->permissions()->pluck('slug');
        $permissions = Permission::whereIn('slug', $allowedPermissionSlugs)->get();
    }

    $permissionRole = $role->permissions()->get();

    return Inertia::render('pages/roles/permission', [
        'role' => $role,
        'permissions' => $permissions,
        'permissionRole' => $permissionRole,
    ]);
}

    public function store(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = Permission::whereIn('slug', $request->permissions)->pluck('id');

        $role->permissions()->sync($permissions);


        // return redirect()->route('roles.index');
        return response()->json([
            'message' => 'Permissions synced successfully',
        ]);
    }


    /**
     * User's permissions
     * 
     * */
    // public function userPermissions()
    // {

    //     $role = auth()->user()->roles()->first();
    //     // dd($role);

    //     if($role->slug=='super-admin'){

    //       $permissions = Permission::pluck('slug')->all();  

    //     }else{

    //     $permissions = $role->permissions()->pluck('slug')->toArray();

    //     }
    //     // dd($permissions);


    //     return response()->json([
    //         'success'=>true,
    //         'message' => 'Permissions listed successfully',
    //         'data'=>$permissions
    //     ]);


    // }
    public function userPermissions()
{
    $role = auth()->user()->roles()->first();

    if ($role->slug === 'super-admin') {
        // Super admin can see all permissions
        $permissions = Permission::pluck('slug')->all();
    } else {
        // Other admins see only the permissions assigned to their role
        $permissions = $role->permissions()->pluck('slug')->toArray();
    }

    return response()->json([
        'success' => true,
        'message' => 'Permissions listed successfully',
        'data' => $permissions
    ]);
}
}

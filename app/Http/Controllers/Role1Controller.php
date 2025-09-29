<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DB;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;

class Role1Controller extends Controller
{
    public function index()
    {
        return Inertia::render('pages/roles1/index');
    }


    public function list(QueryFilterContract $queryFilter){

        $query = Role::query();

        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

        return response()->json([
            'roles' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:3',
            'description' => 'required|unique:roles',
        ]);

        $role = Role::create($validatedData);

        return response()->json([
            'roles' => $role,
            'successMessage' => 'Role created successfully',
        ], 201);
    }

    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required|unique:roles,description,' . $role->id,
        ]);

        $role->update($validatedData);

        return response()->json([
            'roles' => $role,
            'successMessage' => 'Role updated successfully',
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'successMessage' => 'Role deleted successfully',
        ]);
    }
}

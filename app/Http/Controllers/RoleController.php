<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Access\Role;
use App\Base\Constants\Auth\Role as RoleSlug;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $adminCreatedRoles = Role::whereNull('created_by')->get();
        $superAdmin = auth()->user();
        foreach ($adminCreatedRoles as $key => $role) {
            $role->update(['created_by'=>$superAdmin->id]);
        }
        return Inertia::render('pages/roles/index');
    }


    public function getRoles(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $searchQuery = $request->input('searchQuery');
        $user = auth()->user(); // Get the authenticated user
    
        $roleQuery = Role::where('created_by', $user->id) // Filter roles by the creator
                         ->where('slug', '!=', 'super-admin')->whereNotIn('slug', RoleSlug::mobileAppRoles());
    

       

        if ($searchQuery) {
            $roleQuery->where(function ($query) use ($searchQuery) {
                $query->where('slug', 'like', "%$searchQuery%")
                      ->orWhere('name', 'like', "%$searchQuery%")
                      ->orWhere('description', 'like', "%$searchQuery%");
            });
        }
    
        $roles = $roleQuery->latest()->paginate($perPage);
    
        return response()->json(['roles' => $roles]);
    }


    public function store(Request $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $currentUser = auth()->user();
        $validatedData['slug'] = str_replace(' ', '-', strtolower($validatedData['name']));
        $validatedData['created_by'] = $currentUser->id; // Track creator of the role

        $role = Role::create($validatedData);

        return response()->json([
            'role' => $role,
            'successMessage' => 'Role created successfully!',
        ], 201);
    }



public function convertToSnakeCase(string $str): string
{
  // Convert string to lowercase
  $str = strtolower($str);

  // Replace spaces and non-alphanumeric characters with underscores
  return preg_replace('/\s+|[^\w]/', '-', $str);
}


    public function update(Request $request, Role $role)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $role->update($validatedData);

        return response()->json([
            'role' => $role,
            'successMessage' => 'Role updated successfully',
        ]);
    }

}

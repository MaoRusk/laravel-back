<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:roles.show', only: ['show']),
            new Middleware('permission:roles.create', only: ['store']),
            new Middleware('permission:roles.update', only: ['update']),
            new Middleware('permission:roles.destroy', only: ['destroy'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        return $this->response('Roles obtained successfully', $roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer'
        ]);

        if (Role::where('name', $validated['name'])->count()) {
            return $this->error('The role name already exists in the system');
        }

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web'
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $validated['permissions'])->pluck('name', 'id')->toArray();

            $role->syncPermissions(array_keys($permissions));

            $firstPermission = reset($permissions);
            [$module] = explode('.', $firstPermission);

            $hasUpdate = in_array("$module.update", $permissions);
            $hasShow = in_array("$module.show", $permissions);

            if ($hasUpdate && !$hasShow) {
                $showPermissionId = \Spatie\Permission\Models\Permission::where('name', "$module.show")->value('id');
                if ($showPermissionId) {
                    $role->givePermissionTo($showPermissionId);
                }
            }

            if (!$hasUpdate && $hasShow) {
                $role->revokePermissionTo("$module.show");
            }
        }

        return $this->response('Role created successfully', $role);
    }

    /**
     * Display the specified resource.
     */
    public function show($roleId)
    {
        $role = Role::with(['permissions'])->findOrFail($roleId);
        return $this->response('Role successfully obtained', $role);
    }


    /**
     * @param Request $request
     * @param $roleId
     * @return \App\Responses\ApiResponse
     */
    public function update(Request $request, $roleId): ApiResponse
    {
        $role = Role::findOrFail($roleId);

        $validated = $request->validate([
            'name' => 'required',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer'
        ]);

        if (Role::where('name', $validated['name'])->where('id', '!=', $role->id)->count()) {
            return $this->error('The role name already exists in the system');
        }

        $role->update(['name' => $validated['name'], 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $validated['permissions'])->pluck('name', 'id')->toArray();

            $role->syncPermissions(array_keys($permissions));

            $firstPermission = reset($permissions);
            [$module] = explode('.', $firstPermission);


            $hasUpdate = in_array("$module.update", $permissions);
            $hasShow = in_array("$module.show", $permissions);


            if ($hasUpdate && !$hasShow) {
                $showPermissionId = \Spatie\Permission\Models\Permission::where('name', "$module.show")->value('id');
                if ($showPermissionId) {
                    $role->givePermissionTo($showPermissionId);
                }
            }


            if (!$hasUpdate && $hasShow) {
                $role->revokePermissionTo("$module.show");
            }
        }

        return $this->response('Role updated successfully', $role);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->delete();
        return $this->response('Role deleted successfully', $role);
    }
}

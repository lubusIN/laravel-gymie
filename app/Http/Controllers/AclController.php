<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use Illuminate\Http\Request;
use jeremykenedy\LaravelRoles\Models\Permission;
use jeremykenedy\LaravelRoles\Models\Role;

class AclController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * CRUD functions for Users.
     */
    public function userIndex()
    {
        $users = User::excludeArchive()->paginate(10);

        return view('user.userIndex', compact('users'));
    }

    public function createUser()
    {
        $roles = Role::pluck('name', 'id');
        return view('user.createUser', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'status' => $request->status,
        ]);

        $user->save();

        // Adding Photo
        if ($request->hasFile('photo')) {
            $user->addMedia($request->file('photo'))->usingFileName('staff_' . $user->id . $request->photo->getClientOriginalExtension())->toMediaCollection('staff');
        }
        $user->save();

        $user->attachRole($request->role_id);

        flash()->success('User was successfully created');

        return redirect('user');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $rolesWithoutGymie = Role::where('name', '!=', 'Gymie')->pluck('name','id');
        $roles = Role::pluck('name','id');
        return view('user.editUser', compact('user','rolesWithoutGymie','roles'));
    }

    public function updateUser($id, Request $request)
    {
        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;

        if (!empty($request->password)) {
            $this->validate($request, ['password' => 'required|string|min:6|confirmed']);
            $user->password = bcrypt($request->password);
        }

        $user->status = $request->status;

        $user->update();

        if ($request->hasFile('photo')) {
            $user->clearMediaCollection('staff');
            $user->addMedia($request->file('photo'))->usingFileName('staff_' . $user->id . $request->photo->getClientOriginalExtension())->toMediaCollection('staff');
        }
        $user->save();

        if ($user->roles[0]->id != $request->role_id) {
            // RoleUser::where('user_id', $user->id)->where('role_id', $user->roleUser->role_id)->delete();
            $user->detachAllRoles();
            $user->attachRole($request->role_id);
        }

        flash()->success('User details were successfully updated');

        return redirect('user');
    }

    public function deleteUser($id)
    {
        DB::beginTransaction();
        try {
            RoleUser::where('user_id', $id)->delete();
            $user = User::findOrFail($id);
            $user->clearMediaCollection('staff');
            $user->status = \constStatus::Archive;
            $user->save();

            DB::commit();
            flash()->success('User was successfully deleted');

            return redirect('user');
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('User was not deleted');

            return redirect('user');
        }
    }

    /**
     * CRUD functions for Roles.
     */
    public function roleIndex()
    {
        $roles = Role::where('name', '!=', 'Gymie')->get();

        return view('user.roleIndex', compact('roles'));
    }

    public function createRole()
    {
        $permissions = Permission::all();

        return view('user.createRole', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name'        => $request->name,
                'slug'        => $request->display_name,
                'description' => $request->description,
                'level'       => 1
            ]);

            if ($request->has('permissions')) {
                foreach ($request->permissions as $addPerm) {
                    $role->attachPermission($addPerm);
                }
            }

            DB::commit();
            flash()->success('Role was successfully created');

            return redirect('user/role');
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Role was not created');

            return redirect('user/role');
        }
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $permission_role = $role->permissions;
        // dd($permission_role);
        return view('user.editRole', compact('role', 'permissions', 'permission_role'));
    }

    public function updateRole($id, Request $request)
    {
        DB::beginTransaction();
        try {
            //Updating Role
            $role = Role::findOrFail($id);

            $role->update([
                'name' => $request->name,
                'slug' => $request->display_name,
                'description' => $request->description,
            ]);

            //Updating permissions for the role
            $DBpermissions = $role->permissions()->pluck('permissions.id');
            $ClientPermissions = collect($request->permissions);

            $addPermissions = $ClientPermissions->diff($DBpermissions);
            $deletePermissions = $DBpermissions->diff($ClientPermissions);

            if ($addPermissions->count()) {
                foreach ($addPermissions as $addPerm) {
                    $role->attachPermission($addPerm);
                }
            }

            if ($deletePermissions->count()) {
                foreach ($deletePermissions as $deletePermission) {
                    // PermissionRole::where('role_id', $id)->where('permission_id', $deletePermission)->delete();
                    $role->detachPermission($deletePermission);
                }
            }

            DB::commit();
            flash()->success('Role was successfully updated');

            return redirect('user/role');
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Role was not updated');

            return redirect('user/role');
        }
    }

    public function deleteRole($id)
    {
        DB::beginTransaction();
        try {
            Permission_role::where('role_id', $id)->delete();
            Role::where('id', $id)->delete();

            DB::commit();
            flash()->success('Role was successfully deleted');

            return redirect('user/role');
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Role was not deleted');

            return redirect('user/role');
        }
    }

    /**
     * CRUD functions for Permissions.
     */
    public function permissionIndex()
    {
        $permissions = Permission::all();

        return view('user.permissionIndex', compact('permissions'));
    }

    public function createPermission()
    {
        return view('user.createPermission');
    }

    public function storePermission(Request $request)
    {
        Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'group_key' => $request->group_key,
        ]);

        flash()->success('Permission was successfully created');

        return redirect('user/permission');
    }

    public function editPermission($id)
    {
        $permission = Permission::findOrFail($id);

        return view('user.editPermission', compact('permission'));
    }

    public function updatePermission($id, Request $request)
    {
        $permission = Permission::findOrFail($id);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'group_key' => $request->group_key,
        ]);

        flash()->success('Permission was successfully updated');

        return redirect('user/permission');
    }

    public function deletePermission($id)
    {
        Permission::findOrFail($id)->delete();

        flash()->success('Permission was successfully deleted');

        return redirect('user/permission');
    }
}

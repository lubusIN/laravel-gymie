<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Role;
use App\User;
use App\RoleUser;
use App\Permission;
use App\Permission_role;
use Illuminate\Http\Request;

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
        return view('user.createUser');
    }

    public function storeUser(Request $request)
    {
        $this->validate($request, ['name' => 'required|max:255',
                                   'email' => 'required|email|max:255|unique:mst_users',
                                   'password' => 'required|confirmed|min:6', ]);

        $user = User::create(['name' => $request['name'],
                                'email' => $request['email'],
                                'password' => bcrypt($request['password']),
                              'status'=> $request->status, ]);

        $user->save();

        // Adding Photo
        if ($request->hasFile('photo')) {
            $user->addMedia($request->file('photo'))->usingFileName('staff_'.$user->id.$request->photo->getClientOriginalExtension())->toCollection('staff');
        }
        $user->save();

        $user->attachRole($request->role_id);

        flash()->success('User was successfully created');

        return redirect('user');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);

        return view('user.editUser', compact('user'));
    }

    public function updateUser($id, Request $request)
    {
        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;

        if (! empty($request->password)) {
            $this->validate($request, ['password' => 'required|string|min:6|confirmed']);
            $user->password = bcrypt($request->password);
        }

        $user->status = $request->status;

        $user->update();

        if ($request->hasFile('photo')) {
            $user->clearMediaCollection('staff');
            $user->addMedia($request->file('photo'))->usingFileName('staff_'.$user->id.$request->photo->getClientOriginalExtension())->toCollection('staff');
        }
        $user->save();

        if ($user->role_user->role->id != $request->role_id) {
            RoleUser::where('user_id', $user->id)->where('role_id', $user->role_user->role_id)->delete();
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
        $roles = Role::excludeGymie()->get();

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
            $role = Role::create(['name' => $request->name,
                                  'display_name' => $request->display_name,
                                  'description' => $request->description,
                                 ]);

            if ($request->has('permissions')) {
                $role->attachPermissions($request->permissions);
            }

            DB::commit();
            flash()->success('Role was successfully created');

            return redirect('role');
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Role was not created');

            return redirect('role');
        }
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $permission_role = Permission_role::where('role_id', $id)->get();

        return view('user.editRole', compact('role', 'permissions', 'permission_role'));
    }

    public function updateRole($id, Request $request)
    {
        DB::beginTransaction();
        try {
            //Updating Role
            $role = Role::findOrFail($id);

            $role->update(['name' => $request->name,
                           'display_name' => $request->display_name,
                           'description' => $request->description,
                          ]);

            //Updating permissions for the role
            $DBpermissions = Permission_role::where('role_id', $id)->select('permission_id')->lists('permission_id');
            $ClientPermissions = collect($request->permissions);

            $addPermissions = $ClientPermissions->diff($DBpermissions);
            $deletePermissions = $DBpermissions->diff($ClientPermissions);

            if ($addPermissions->count()) {
                $role->attachPermissions($addPermissions);
            }

            if ($deletePermissions->count()) {
                foreach ($deletePermissions as $deletePermission) {
                    Permission_role::where('role_id', $id)->where('permission_id', $deletePermission)->delete();
                }
            }

            DB::commit();
            flash()->success('Role was successfully updated');

            return redirect('role');
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Role was not updated');

            return redirect('role');
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

            return redirect('role');
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Role was not deleted');

            return redirect('role');
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
        Permission::create(['name' => $request->name,
                            'display_name' => $request->display_name,
                            'description' => $request->description,
                            'group_key' => $request->group_key,
                           ]);

        flash()->success('Permission was successfully created');

        return redirect('permission');
    }

    public function editPermission($id)
    {
        $permission = Permission::findOrFail($id);

        return view('user.editPermission', compact('permission'));
    }

    public function updatePermission($id, Request $request)
    {
        $permission = Permission::findOrFail($id);

        $permission->update(['name' => $request->name,
                            'display_name' => $request->display_name,
                            'description' => $request->description,
                            'group_key' => $request->group_key,
                            ]);

        flash()->success('Permission was successfully updated');

        return redirect('permission');
    }

    public function deletePermission($id)
    {
        Permission::findOrFail($id)->delete();

        flash()->success('Permission was successfully deleted');

        return redirect('permission');
    }
}

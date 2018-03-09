<?php

namespace App\Http\Controllers;

use App\User;
use Lubus\Constants\Status;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = user::excludeArchive()->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
        $user = user::findOrFail($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['email' => 'unique:mst_users,email']);

        $user = ['name'=>$request->name,
                    'email'=> $request->email,
                    'photo'=> '',
                    'password' => bcrypt($request->password),
                    'status'=> $request->status, ];
        $user = new User($user);
        $user->save();

        if ($user->id) {
            $user->photo = \constFilePrefix::StaffPhoto.$user->id.'.jpg';
            $user->save();
            \Utilities::uploadFile($request, \constFilePrefix::StaffPhoto, $user->id, 'photo', \constPaths::StaffPhoto);

            flash()->success('User was successfully registered');

            return redirect('users');
        } else {
            flash()->error('Error while user registration');

            return redirect('users');
        }
    }

    public function edit($id)
    {
        $user = user::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update($id, Request $request)
    {
        $user = user::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password != '') {
            $user->password = bcrypt($request->password);
        }

        $user->status = $request->status;

        $user->update();
        $user->photo = \constFilePrefix::staffPhoto.$user->id.'.jpg';
        $user->save();

        \Utilities::uploadFile($request, \constFilePrefix::staffPhoto, $user->id, 'photo', \constPaths::staffPhoto);

        flash()->success('User details was successfully updated');

        return redirect('users');
    }

    public function archive($id, Request $request)
    {
        $user = user::findOrFail($id);
        $user->status = \constStatus::Archive;
        $user->save();
        flash()->error('User was successfully deleted');

        return redirect('users');
    }
}

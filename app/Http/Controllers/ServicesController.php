<?php

namespace App\Http\Controllers;

use Auth;
use App\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $services = Service::search('"'.$request->input('search').'"')->paginate(10);
        $count = $services->count();

        return view('services.index', compact('services', 'count'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
        $service = Service::findOrFail($id);

        return view('services.show', compact('service'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //Model Validation
        $this->validate($request, ['name' => 'unique:mst_services,name']);

        $service = new Service($request->all());

        $service->createdBy()->associate(Auth::user());
        $service->updatedBy()->associate(Auth::user());

        $service->save();

        flash()->success('Service was successfully created');

        return redirect('plans/services/all');
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);

        return view('services.edit', compact('service'));
    }

    public function update($id, Request $request)
    {
        $service = Service::findOrFail($id);

        $service->update($request->all());
        $service->updatedBy()->associate(Auth::user());
        $service->save();
        flash()->success('Service details were successfully updated');

        return redirect('plans/services/all');
    }

    public function delete($id)
    {
        Service::destroy($id);

        return redirect('plans/services/all');
    }
}

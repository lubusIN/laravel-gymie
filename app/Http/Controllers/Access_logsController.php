<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\access_log;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class access_logsController extends Controller
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
    public function index()
    {
    	$access_logs = Access_log::all();

    	return view('access_logs.index', compact('access_logs'));
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
    	$access_log = Access_log::findOrFail($id);

    	return view('access_logs.show', compact('access_log'));
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
    	return view('access_logs.create');
    }
    
     /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
    	Access_log::create(Request::all());

    	return redirect('access_logs'); 
    }



}

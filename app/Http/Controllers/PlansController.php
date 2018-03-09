<?php

namespace App\Http\Controllers;

use Auth;
use App\Plan;
use Illuminate\Http\Request;

class PlansController extends Controller
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
        $plans = Plan::excludeArchive()->search('"'.$request->input('search').'"')->paginate(10);
        $planTotal = Plan::excludeArchive()->search('"'.$request->input('search').'"')->get();
        $count = $planTotal->count();

        return view('plans.index', compact('plans', 'count'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
        $plan = Plan::findOrFail($id);

        return view('plans.show', compact('plan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('plans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //Model Validation
        $this->validate($request, ['plan_code' => 'unique:mst_plans,plan_code',
                                   'plan_name' => 'unique:mst_plans,plan_name', ]);

        $plan = new Plan($request->all());

        $plan->createdBy()->associate(Auth::user());
        $plan->updatedBy()->associate(Auth::user());

        $plan->save();

        flash()->success('Plan was successfully created');

        return redirect('plans');
    }

    public function edit($id)
    {
        $plan = Plan::findOrFail($id);

        return view('plans.edit', compact('plan'));
    }

    public function update($id, Request $request)
    {
        $plan = Plan::findOrFail($id);

        $plan->update($request->all());
        $plan->updatedBy()->associate(Auth::user());
        $plan->save();
        flash()->success('Plan details were successfully updated');

        return redirect('plans/all');
    }

    public function archive($id)
    {
        Plan::destroy($id);

        return redirect('plans/all');
    }
}

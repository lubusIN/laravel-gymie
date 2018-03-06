<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use App\plan;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PlansController extends Controller
{
    public function index()
    {
        $plans = plan::excludeArchive()->get();

    	return response()->json($plans);
    }

    public function show($id)
    {
        $plan = plan::findOrFail($id);

        return response()->json($plan);
    }
}
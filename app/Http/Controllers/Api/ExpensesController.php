<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use App\expense;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExpensesController extends Controller
{
    public function index()
    {
        $expenses = expense::all();

    	return response()->json($expenses);
    }

    public function show($id)
    {
        $expense = expense::findOrFail($id);

        return response()->json($expense);
    }
}
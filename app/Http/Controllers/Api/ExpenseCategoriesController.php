<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use App\expenseCategory;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExpenseCategoriesController extends Controller
{
    public function index()
    {
        $expenseCategories = expenseCategory::excludeArchive()->get();

    	return response()->json($expenseCategories);
    }

    public function show($id)
    {
        $expenseCategory = expenseCategory::findOrFail($id);

        return response()->json($expenseCategory);
    }
}
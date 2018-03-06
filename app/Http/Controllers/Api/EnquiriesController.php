<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\enquiry;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class EnquiriesController extends Controller
{
	public function index()
	{
		$enquiries = enquiry::all();

		return response()->json($enquiries);
	}

	public function show($id)
	{
		$enquiry = enquiry::findOrFail($id);

		return response()->json($enquiry);
	}
}
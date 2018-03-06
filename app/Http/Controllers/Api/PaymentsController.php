<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\payment_detail;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
	public function index()
	{
		$payment_details = payment_detail::all();

		return response()->json($payment_details);
	}

	public function show($id)
	{
		$payment_detail = payment_detail::findOrFail($id);

		return response()->json($payment_detail);
	}
}
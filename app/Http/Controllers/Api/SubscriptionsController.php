<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\subscription;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubscriptionsController extends Controller
{
	public function index()
	{
		$subscriptions = subscription::all();

		return response()->json($subscriptions);
	}

	public function expiring()
	{
		$expiring = subscription::expiring()->get();

		return response()->json($expiring);
	}

	public function expired()
	{
		$expired = subscription::expired()->get();

		return response()->json($expired);
	}

	public function show($id)
	{
		$subscription = subscription::findOrFail($id);

		return response()->json($subscription);
	}
}
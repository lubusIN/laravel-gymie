<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\setting;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
	public function index()
	{
		$settings = \Utilities::getSettings();

		return response()->json($settings);
	}
}
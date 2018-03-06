<?php

namespace App\Http\Controllers\ReportData;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\member;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MembersController extends Controller
{
	public function details()
	{
		$members = Member::all();
		$data = ["members_data" => $members];

		return response()->json($data);	
	}
}
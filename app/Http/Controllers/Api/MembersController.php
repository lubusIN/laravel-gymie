<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\member;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MembersController extends Controller
{
	public function index()
	{
		$members = Member::excludeArchive()->get();

		return response()->json($members);
	}

	public function show($id)
	{
		$member = Member::findOrFail($id);

		return response()->json($member);
	}
}
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use JavaScript;
use DB;
use Carbon\Carbon;
use App\member;
use App\setting;
use App\invoice;
use App\payment_detail;
use App\plan;
use App\enquiry;
use App\followup;
use App\subscription;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
	public function index()
	{
		$active = Member::where('status',1)->count();
		$inActive = Member::where('status',0)->count();
		$expiring = subscription::expiring()->count();
        $expired = subscription::expired()->count();
        $outstanding = invoice::sum('pending_amount');
        $collection = payment_detail::sum('payment_amount');

		return response()->json(['active' => $active,'inActive' => $inActive,'expiring' => $expiring,'expired' => $expired,'outstanding' => $outstanding,'collection' => $collection]);
	}
}
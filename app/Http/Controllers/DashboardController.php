<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JavaScript;
use DB;
use Auth;
use Carbon\Carbon;
use App\Member;
use App\Setting;
use App\Invoice;
use App\Plan;
use App\Expense;
use App\Enquiry;
use App\Followup;
use App\Subscription;
use App\Sms_log;
use App\Payment_detail;
use App\Cheque_detail;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        JavaScript::put([
            'jsRegistraionsCount' => \Utilities::registrationsTrend(),
            'jsMembersPerPlan' => \Utilities::membersPerPlan(),
        ]);

        $expirings = Subscription::dashboardExpiring()->paginate(5);
        $expiringTotal = Subscription::dashboardExpiring()->get();
        $expiringCount = $expiringTotal->count();
        $allExpired = Subscription::dashboardExpired()->paginate(5);
        $allExpiredTotal = Subscription::dashboardExpired()->get();
        $expiredCount = $allExpiredTotal->count();
        $birthdays = Member::birthday()->get();
        $birthdayCount = $birthdays->count();
        $recents = Member::recent()->get();
        $enquiries = Enquiry::onlyLeads()->get();
        $reminders = Followup::reminders()->get();
        $reminderCount = $reminders->count();
        $dues = Expense::dueAlerts()->get();
        $outstandings = Expense::outstandingAlerts()->get();
        $smsRequestSetting = \Utilities::getSetting('sms_request');
        $smslogs = Sms_log::dashboardLogs()->get();
        $recievedCheques = Cheque_detail::where('status',\constChequeStatus::Recieved)->get();
        $recievedChequesCount = $recievedCheques->count();
        $depositedCheques = Cheque_detail::where('status',\constChequeStatus::Deposited)->get();
        $depositedChequesCount = $depositedCheques->count();
        $bouncedCheques = Cheque_detail::where('status',\constChequeStatus::Bounced)->get();
        $bouncedChequesCount = $bouncedCheques->count();
        $membersPerPlan =  json_decode(\Utilities::membersPerPlan());

		return view('dashboard.index',compact('expirings','allExpired','birthdays','recents','enquiries','reminders','dues','outstandings','smsRequestSetting','smslogs','expiringCount','expiredCount','birthdayCount','reminderCount','recievedCheques','recievedChequesCount','depositedCheques','depositedChequesCount','bouncedCheques','bouncedChequesCount','membersPerPlan'));
    }

    public function smsRequest(Request $request)
    {
        $contact = 9820461665;
        $sms_text = "A request for ".$request->smsCount." sms has came from ".\Utilities::getSetting('gym_name')." by ".Auth::user()->name;
        $sms_status = 1;
        \Utilities::Sms($contact,$sms_text,$sms_status);

        Setting::where('key', '=','sms_request')->update(['value' => 1]);

        flash()->success('Request has been successfully sent, a confirmation call will be made soon');
        return redirect('/dashboard');
    }

}

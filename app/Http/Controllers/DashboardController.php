<?php

namespace App\Http\Controllers;

use Auth;
use App\Member;
use App\SmsLog;
use JavaScript;
use App\Enquiry;
use App\Expense;
use App\Setting;
use App\Followup;
use App\ChequeDetail;
use App\Subscription;
use Illuminate\Http\Request;

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
        $expiringCount = $expirings->total();
        $allExpired = Subscription::dashboardExpired()->paginate(5);
        $expiredCount = $allExpired->total();
        $birthdays = Member::birthday()->get();
        $birthdayCount = $birthdays->count();
        $recents = Member::recent()->get();
        $enquiries = Enquiry::onlyLeads()->get();
        $reminders = Followup::reminders()->get();
        $reminderCount = $reminders->count();
        $dues = Expense::dueAlerts()->get();
        $outstandings = Expense::outstandingAlerts()->get();
        $smsRequestSetting = \Utilities::getSetting('sms_request');
        $smslogs = SmsLog::dashboardLogs()->get();
        $recievedCheques = ChequeDetail::where('status', \constChequeStatus::Recieved)->get();
        $recievedChequesCount = $recievedCheques->count();
        $depositedCheques = ChequeDetail::where('status', \constChequeStatus::Deposited)->get();
        $depositedChequesCount = $depositedCheques->count();
        $bouncedCheques = ChequeDetail::where('status', \constChequeStatus::Bounced)->get();
        $bouncedChequesCount = $bouncedCheques->count();
        $membersPerPlan = json_decode(\Utilities::membersPerPlan());

        return view('dashboard.index', compact('expirings', 'allExpired', 'birthdays', 'recents', 'enquiries', 'reminders', 'dues', 'outstandings', 'smsRequestSetting', 'smslogs', 'expiringCount', 'expiredCount', 'birthdayCount', 'reminderCount', 'recievedCheques', 'recievedChequesCount', 'depositedCheques', 'depositedChequesCount', 'bouncedCheques', 'bouncedChequesCount', 'membersPerPlan'));
    }

    public function smsRequest(Request $request)
    {
        $contact = 9820461665;
        $sms_text = 'A request for '.$request->smsCount.' sms has came from '.\Utilities::getSetting('gym_name').' by '.Auth::user()->name;
        $sms_status = 1;
        \Utilities::Sms($contact, $sms_text, $sms_status);

        Setting::where('key', '=', 'sms_request')->update(['value' => 1]);

        flash()->success('Request has been successfully sent, a confirmation call will be made soon');

        return redirect('/dashboard');
    }
}

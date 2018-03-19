<?php

use App\Plan;
use App\Member;
use App\SmsLog;
use App\Setting;
use Carbon\Carbon;
use App\Subscription;
use Illuminate\Http\Request;

class Utilities
{
    public static function setActiveMenu($uri, $isParent = false)
    {
        $class = ($isParent) ? 'active open' : 'active';

        return \Request::is($uri) ? $class : '';
        //return \Request::is($uri);
    }

    // Get Setting
    public static function getSetting($key)
    {
        $settingValue = Setting::where('key', '=', $key)->pluck('value');

        return $settingValue;
    }

    //get Settings
    public static function getSettings()
    {
        $settings = Setting::all();
        $settings_array = [];

        foreach ($settings as $setting) {
            $settings_array[$setting->key] = $setting->value;
        }

        return $settings_array;
    }

    //Follow up Status
    public static function getFollowUpStatus($status)
    {
        switch ($status) {
        case '1':
            return 'Done';
            break;

        default:
            return 'Pending';
            break;
    }
    }

    //Follow up by
    public static function getFollowupBy($followUpBy)
    {
        switch ($followUpBy) {
        case '1':
            return 'SMS';
            break;

        case '2':
            return 'Personal';
            break;

        default:
            return 'Call';
            break;
    }
    }

    //FollowUp Status Icon bg
    public static function getIconBg($status)
    {
        switch ($status) {
        case '1':
            return 'bg-blue-400 border-blue-700';
            break;

        default:
            return 'bg-orange-400 border-orange-700';
            break;
    }
    }

    //Followup Status Icon
    public static function getStatusIcon($status)
    {
        switch ($status) {
        case '1':
            return 'fa fa-thumbs-up';
            break;

        default:
            return 'fa fa-refresh';
            break;
    }
    }

    // Aim for member & enquiry creation
    public static function getAim($aim)
    {
        switch ($aim) {
        case '1':
            return 'Networking';
            break;

        case '2':
            return 'Body Building';
            break;

        case '3':
            return 'Fatloss';
            break;

        case '4':
            return 'Weightgain';
            break;

        case '5':
            return 'Others';
            break;

        default:
            return 'Fitness';
            break;
    }
    }

    // Invoice Labels
    public static function getInvoiceLabel($status)
    {
        switch ($status) {
        case '0':
            return 'label label-danger';
            break;

        case '1':
            return 'label label-success';
            break;

        case '3':
            return 'label label-default';
            break;

        default:
            return 'label label-primary';
            break;
    }
    }

    // Expense alert repeat
    public static function expenseRepeatIntervel($repeat)
    {
        switch ($repeat) {
        case '0':
            return 'Never Repeat';
            break;

        case '1':
            return 'Every Day';
            break;

        case '2':
            return 'Every Week';
            break;

        case '3':
            return 'Every Month';
            break;

        default:
            return 'Every Year';
            break;
    }
    }

    //Paid Unpaid Labels
    public static function getPaidUnpaid($status)
    {
        switch ($status) {
        case '0':
            return 'label label-danger';
            break;

        default:
            return 'label label-primary';
            break;
    }
    }

    //Active-Inactive Labels
    public static function getActiveInactive($status)
    {
        switch ($status) {
        case '0':
            return 'label label-danger';
            break;

        default:
            return 'label label-primary';
            break;
    }
    }

    // Occupation of members
    public static function getOccupation($occupation)
    {
        switch ($occupation) {
        case '1':
            return 'Housewife';
            break;

        case '2':
            return 'Self Employed';
            break;

        case '3':
            return 'Professional';
            break;

        case '4':
            return 'Freelancer';
            break;

        case '5':
            return 'Others';
            break;

        default:
            return 'Student';
            break;
    }
    }

    // Source for member & enquiry creation
    public static function getSource($source)
    {
        switch ($source) {
        case '1':
            return 'Word of mouth';
            break;

        case '2':
            return 'Others';
            break;

        default:
            return 'Promotions';
            break;
    }
    }

    // Member Status
    public static function getStatusValue($status)
    {
        switch ($status) {
        case '0':
            return 'Inactive';
            break;

        case '2':
            return 'Archived';
            break;

        default:
            return 'Active';
            break;
    }
    }

    // Enquiry Status
    public static function getEnquiryStatus($status)
    {
        switch ($status) {
        case '0':
            return 'Lost';
            break;

        case '2':
            return 'Member';
            break;

        default:
            return 'Lead';
            break;
    }
    }

    // Enquiry Label
    public static function getEnquiryLabel($status)
    {
        switch ($status) {
        case '0':
            return 'label label-danger';
            break;

        case '2':
            return 'label label-success';
            break;

        default:
            return 'label label-primary';
            break;
    }
    }

    // Set invoice status
    public static function setInvoiceStatus($amount_due, $invoice_total)
    {
        if ($amount_due == 0) {
            $paymentStatus = \constPaymentStatus::Paid;
        } elseif ($amount_due > 0 && $amount_due < $invoice_total) {
            $paymentStatus = \constPaymentStatus::Partial;
        } elseif ($amount_due == $invoice_total) {
            $paymentStatus = \constPaymentStatus::Unpaid;
        } else {
            $paymentStatus = \constPaymentStatus::Overpaid;
        }

        return $paymentStatus;
    }

    // Invoice Status
    public static function getInvoiceStatus($status)
    {
        switch ($status) {
        case '1':
            return 'Paid';
            break;

        case '2':
            return 'Partial';
            break;

        case '3':
            return 'Overpaid';
            break;

        default:
            return 'Unpaid';
            break;
    }
    }

    // Subcription Status
    public static function getSubscriptionStatus($status)
    {
        switch ($status) {
        case '0':
            return 'Expired';
            break;

        case '2':
            return 'Renewed';
            break;

        case '3':
            return 'Cancelled';
            break;

        default:
            return 'OnGoing';
            break;
    }
    }

    // Subcription Label
    public static function getSubscriptionLabel($status)
    {
        switch ($status) {
        case '0':
            return 'label label-danger';
            break;

        case '2':
            return 'label label-success';
            break;

        case '3':
            return 'label label-default';
            break;

        default:
            return 'label label-primary';
            break;
    }
    }

    // Payment Mode
    public static function getPaymentMode($status)
    {
        switch ($status) {
        case '0':
            return 'Cheque';
            break;

        default:
            return 'Cash';
            break;
    }
    }

    // Cheque status
    public static function getChequeStatus($status)
    {
        switch ($status) {
        case '1':
            return 'Deposited';
            break;

        case '2':
            return 'Cleared';
            break;

        case '3':
            return 'Bounced';
            break;

        case '4':
            return 'Reissued';
            break;

        default:
            return 'Recieved';
            break;
    }
    }

    // Get Gender
    public static function getGender($gender)
    {
        switch ($gender) {
        case 'm':
            return 'Male';
            break;

        case 'f':
            return 'Female';
            break;
    }
    }

    //Get invoice display name type
    public static function getDisplay($display)
    {
        switch ($display) {
        case 'gym_logo':
            return 'Gym Logo';
            break;

        default:
            return 'Gym Name';
            break;
    }
    }

    // Get Numbering mode
    public static function getMode($mode)
    {
        switch ($mode) {
        case '0':
            return 'Manual';
            break;

        default:
            return 'Automatic';
            break;
    }
    }

    public static function getGreeting()
    {
        //$time = date("H");
        $time = Carbon::now()->hour;
        /* If the time is less than 1200 hours, show good morning */
        if ($time < '12') {
            echo 'Good morning';
        } elseif /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
    ($time >= '12' && $time < '17') {
            echo 'Good afternoon';
        } elseif /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
    ($time >= '17' && $time < '22') {
            echo 'Good evening';
        } elseif /* Finally, show good night if the time is greater than or equal to 2200 hours */
    ($time >= '22') {
            echo 'Good night';
        }
    }

    /**
     *File Upload.
     **/
    public static function uploadFile(Request $request, $prefix, $recordId, $upload_field, $upload_path)
    {
        if ($request->hasFile($upload_field)) {
            $file = $request->file($upload_field);

            if ($file->isValid()) {
                File::delete(public_path('assets/img/gym/gym_logo.jpg'));
                $fileName = 'gym_logo.jpg';
                $destinationPath = public_path($upload_path);
                $request->file($upload_field)->move($destinationPath, $fileName);
                Image::make($destinationPath.'/'.$fileName)->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save();
            }
        }
    }

    public static function registrationsTrend()
    {
        // Get Financial date
        $startDate = new Carbon(Setting::where('key', '=', 'financial_start')->pluck('value'));
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            //$members = member::registrations($startDate->month,$startDate->year); // Laravel Scoped Query Issue: Workaroud Needed
            $members = Member::whereMonth('created_at', '=', $startDate->month)->whereYear('created_at', '=', $startDate->year)->count();
            $data[] = ['month' => $startDate->format('Y-m'), 'registrations' => $members];
            $startDate->addMonth();
        }

        return json_encode($data);
    }

    public static function membersPerPlan()
    {
        $data = [];

        $plans = Plan::onlyActive()->get();

        foreach ($plans as $plan) {
            $subscriptions = Subscription::where('status', '=', \constSubscription::onGoing)->where('plan_id', '=', $plan->id)->count();
            $data[] = ['label' =>$plan->plan_name, 'value'=>$subscriptions];
        }

        return json_encode($data);
    }

    // Checking logonutility gateway status
    public static function smsGatewayStatus()
    {
        try {
            $api_key = self::getSetting('sms_api_key');

            $api_url = 'http://logonutility.in/app/miscapi/'.$api_key.'/getBalance/true/';

            if (self::isDomainAvailible($api_url)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // returns true, if domain is availible, false if not
    public static function isDomainAvailible($domain)
    {
        //check, if a valid url is provided
        if (! filter_var($domain, FILTER_VALIDATE_URL)) {
            return false;
        }

        //initialize curl
        $curlInit = curl_init($domain);
        curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curlInit, CURLOPT_HEADER, true);
        curl_setopt($curlInit, CURLOPT_NOBODY, true);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

        //get answer
        $response = curl_exec($curlInit);

        curl_close($curlInit);

        if ($response) {
            return true;
        }

        return false;
    }

    public static function Sms($sender_id, $member_contact, $sms_text, $sms_status)
    {
        $sms = self::getSetting('sms');
        $gatewayStatus = self::smsGatewayStatus();

        if ($sms && $sms_status) {
            if ($gatewayStatus) {
                $api_key = self::getSetting('sms_api_key');
                $contacts = $member_contact;
                $from = $sender_id;
                $send_sms_text = urlencode($sms_text);

                //Transactional = 20 , Promo with sender id = 21 , only promotional = 18
                $api_url = 'http://www.logonutility.in/app/smsapi/index.php?key='.$api_key.'&campaign=1&routeid=20&type=text&contacts='.$contacts.'&senderid='.$from.'&msg='.$send_sms_text;

                //Submit to server
                $response = file_get_contents($api_url);

                if (str_contains($response, 'SMS-SHOOT-ID')) {
                    //Log entry for SMS_log table
                    $SmsLogData = ['shoot_id' => substr($response, strpos($response, 'SMS-SHOOT-ID/') + 13),
                                        'number' => $member_contact,
                                        'message' => $sms_text,
                                        'sender_id' => $sender_id,
                                        'send_time' => Carbon::now(),
                                        'status' => 'NA', ];

                    $SmsLog = new SmsLog($SmsLogData);
                    $SmsLog->save();
                }
                //Update SMS balance
                self::smsBalance();
            } else {
                $SmsLogData = ['shoot_id' => '',
                                    'number' => $member_contact,
                                    'message' => $sms_text,
                                    'sender_id' => $sender_id,
                                    'send_time' => Carbon::now(),
                                    'status' => 'offline', ];

                $SmsLog = new SmsLog($SmsLogData);
                $SmsLog->save();
            }
        }
    }

    public static function retrySms($sender_id, $member_contact, $sms_text, $log)
    {
        $gatewayStatus = self::smsGatewayStatus();

        if ($gatewayStatus) {
            $api_key = self::getSetting('sms_api_key');
            $contacts = $member_contact;
            $from = $sender_id;
            $send_sms_text = urlencode($sms_text);

            //Transactional = 20 , Promo with sender id = 21 , only promotional = 18
            $api_url = 'http://www.logonutility.in/app/smsapi/index.php?key='.$api_key.'&campaign=1&routeid=21&type=text&contacts='.$contacts.'&senderid='.$from.'&msg='.$send_sms_text;

            //Submit to server
            $response = file_get_contents($api_url);

            if (str_contains($response, 'SMS-SHOOT-ID')) {
                //Log entry for SMS_log table
                $log->update(['shoot_id' => substr($response, strpos($response, 'SMS-SHOOT-ID/') + 13),
                              'number' => $member_contact,
                              'message' => $sms_text,
                              'sender_id' => $sender_id,
                              'send_time' => Carbon::now(),
                              'status' => 'NA', ]);
                $log->save();
            }
            //Update SMS balance
            self::smsBalance();
        }
    }

    public static function smsBalance()
    {
        $sms = self::getSetting('sms');
        $gatewayStatus = self::smsGatewayStatus();

        if ($sms && $gatewayStatus) {
            $api_key = self::getSetting('sms_api_key');

            $api_url = 'http://logonutility.in/app/miscapi/'.$api_key.'/getBalance/true/';

            //Submit to server

            $credit_balance = file_get_contents($api_url);
            $balance = json_decode($credit_balance);
            Setting::where('key', '=', 'sms_balance')->update(['value' => $balance[0]->BALANCE]);

            // If balance turns zero turn off SMS
            if ($balance[0]->BALANCE == 0) {
                Setting::where('key', '=', 'sms')->update(['value' => 0]);
            }
        }
    }

    public static function smsStatusUpdate()
    {
        $sms = self::getSetting('sms');
        $gatewayStatus = self::smsGatewayStatus();

        if ($sms && $gatewayStatus) {
            $api_key = self::getSetting('sms_api_key');

            // Retry Offline Msg
            $messages = SmsLog::where('status', 'offline')->get();

            foreach ($messages as $message) {
                self::retrySms($message->sender_id, $message->number, $message->message, $message);
            }

            // Update Status
            $messages = SmsLog::whereNotIn('status', ['Delivered', 'Failed', 'offline'])->get();

            foreach ($messages as $message) {
                $sms_shoot_id = $message->shoot_id;
                $api_url = 'http://logonutility.in/app/miscapi/'.$api_key.'/getDLR/'.$sms_shoot_id;

                //Submit to server
                $response = file_get_contents($api_url);

                $dlr_array = json_decode($response);

                //Update Status
                $message->status = $dlr_array[0]->DLR;
                $message->save();
            }
        }
    }
}

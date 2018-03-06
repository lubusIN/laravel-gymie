<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Sms_trigger;
use App\Sms_event;
use App\Member;
use App\Enquiry;
use App\Sms_log;
use App\Http\Requests;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Lubus\Constants\Status;

class SmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function triggersIndex(Request $request)
    {
    	$triggers = Sms_trigger::search($request->input('search'))->get();

    	return view('sms.triggers.index',compact('triggers'));
    }

    public function triggerUpdate(Request $request)
    {
        $DBtriggers = Sms_trigger::all();
        $Clienttriggers = collect($request->triggers);
        //dd($request->triggers);

    	foreach ($DBtriggers as $trigger) 
        {
            $status = ($Clienttriggers->contains($trigger->id) ? 1 : 0);
            
            Sms_trigger::where('id','=',$trigger['id'])->update(['status' => $status]);
        }

    	flash()->success('Message triggers were successfully updated');
    	return redirect('sms/triggers');
    }

    public function eventsIndex(Request $request)
    {
        $events = Sms_event::search($request->input('search'))->paginate(10);

        return view('sms.events.index',compact('events'));
    }

    public function createEvent()
    {
        return view('sms.events.create');
    }

    public function storeEvent(Request $request)
    {
        $event = new Sms_event($request->all());

        $event->createdBy()->associate(Auth::user());
        $event->updatedBy()->associate(Auth::user());

        $event->save();

        flash()->success('Event was successfully created');

        return redirect('sms/events');
    }

    public function editEvent($id)
    {
        $event = Sms_event::findOrFail($id);

        return view('sms.events.edit', compact('event'));
    }

    public function updateEvent($id, Request $request)
    {
        $event = Sms_event::findOrFail($id);

        $event->update($request->all());
        $event->updatedBy()->associate(Auth::user());
        $event->save();
        flash()->success('SMS events details were successfully updated');
        return redirect('sms/events');
    }

    public function destroyEvent($id)
    {
        $event = Sms_event::findOrFail($id);
        $event->delete();

        flash()->success('SMS event was successfully deleted');
        return redirect('sms/events');
    }

    public function send()
    {
        return view('sms.send');
    }

    public function shoot(Request $request)
    {
        $sms_text = $request->message;
        $sender_id = $request->sender_id;

        foreach($request->send as $sendnow)
        {
            switch ($sendnow) {
                case 0:
                        $recievers = Member::where('status',1)->get();
                        foreach ($recievers as $reciever) 
                        {
                            \Utilities::Sms($sender_id,$reciever->contact,$sms_text,true);
                        }
                        break;

                    case 1:
                        $recievers = Member::where('status',0)->get();
                        foreach ($recievers as $reciever) 
                        {
                            \Utilities::Sms($sender_id,$reciever->contact,$sms_text,true);
                        }
                        break;

                    case 2:
                        $recievers = Enquiry::where('status',1)->get();
                        foreach ($recievers as $reciever) 
                        {
                            \Utilities::Sms($sender_id,$reciever->contact,$sms_text,true);
                        }
                        
                        break;

                    case 3:
                        $recievers = Enquiry::where('status',0)->get();
                        foreach ($recievers as $reciever) 
                        {
                            \Utilities::Sms($sender_id,$reciever->contact,$sms_text,true);
                        }
                        
                        break;

                case 4:
                    if($request->customcontacts != '')
                    {
                        $custom = explode(",",str_replace(" ","",($request->customcontacts)));
                        foreach($custom as $number)
                        {
                            if (starts_with($number,'0')) 
                            {
                                $number = substr($number, 1);
                            }
                            \Utilities::Sms($sender_id,$number,$sms_text,true);
                        }
                    }
                
                default:
                    # code...
                    break;
            }
        }

        flash()->success('Message has been successfully sent');
        return redirect('sms/send');
    }

    public function logIndex(Request $request)
    {
        $smslogs = Sms_log::orderBy('send_time','desc')->search('"'.$request->input('search').'"')->paginate(10);

        return view('sms.log',compact('smslogs'));
    }

    public function logRefresh()
    {
        \Utilities::smsStatusUpdate();

        flash()->success('SMS logs have been refreshed');
        return back();
    }
}
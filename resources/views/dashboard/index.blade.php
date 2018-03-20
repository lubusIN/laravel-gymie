@extends('app')

@section('content')

<?php
use Carbon\Carbon;
?>

<div class="rightside bg-grey-100">
<!-- BEGIN PAGE HEADING -->
<!--<div class="page-head bg-grey-100">
<h1 class="page-title">Dashboard</h1>
</div>-->

<div class="container-fluid">
@include('flash::message')
@permission(['manage-gymie','view-dashboard-quick-stats'])
<!-- Stat Tile  -->
<div class="row margin-top-10">
    <!-- Total Members -->
    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
        <div class="panel bg-light-blue-400">
            <div class="panel-body padding-15-20">
                <div class="clearfix">
                    <div class="pull-left">
                        <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0" data-to="{{ App\Member::where('status',1)->count() }}" data-speed="500" data-refresh-interval="10"></div>
                    </div>

                    <div class="pull-right">
                        <i class="font-size-24 color-light-blue-100 fa fa-users"></i>
                    </div>

                    <div class="clearfix"></div>

                    <div class="pull-left">
                        <div class="display-block color-light-blue-50 font-weight-600">Total Members</div>
                    </div>
                </div>
            </div>
        </div><!-- /.panel -->
    </div>

    <!-- Registrations This Weeks -->
    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
        <div class="panel bg-teal-400">
            <div class="panel-body padding-15-20">
                <div class="clearfix">
                    <div class="pull-left">
                        <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0" data-to="{{ App\Member::whereMonth('created_at','=',Carbon::today()->month)->count() }}" data-speed="500" data-refresh-interval="10"></div>
                    </div>
                    <div class="pull-right">
                        <i class="font-size-24 color-teal-100 fa fa-signal"></i>
                    </div>

                    <div class="clearfix"></div>

                    <div class="pull-left">
                        <div class="display-block color-teal-50 font-weight-600">Monthly Joinings</div>
                    </div>
                </div>
            </div>
        </div><!-- /.panel -->
    </div>

    <!-- Inactive Members -->
    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
        <div class="panel bg-amber-300">
            <div class="panel-body padding-15-20">
                <div class="clearfix">
                    <div class="pull-left">
                        <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0" data-to="{{ App\Member::where('status',0)->count() }}" data-speed="500" data-refresh-interval="10"></div>
                    </div>
                    <div class="pull-right">
                        <i class="font-size-24 color-amber-100 fa fa-exclamation-circle"></i>
                    </div>

                    <div class="clearfix"></div>

                    <div class="pull-left">
                        <div class="display-block color-amber-50 font-weight-600">Inactive Members</div>
                    </div>
                </div>
            </div>
        </div><!-- /.panel -->
    </div>

    <!-- Members Expired -->
    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
        <div class="panel bg-grey-500">
            <div class="panel-body padding-15-20">
                <div class="clearfix">
                    <div class="pull-left">
                        <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0" data-to="{{ App\Subscription::where('status',0)->count() }}" data-speed="500" data-refresh-interval="10"></div>
                    </div>
                    <div class="pull-right">
                        <i class="font-size-24 color-grey-100 fa fa-ban"></i>
                    </div>

                    <div class="clearfix"></div>

                    <div class="pull-left">
                        <div class="display-block color-grey-50 font-weight-600">Membership Due</div>
                    </div>
                </div>
            </div>
        </div><!-- /.panel -->
    </div>

    <!-- Outstanding Payments -->
    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
        <div class="panel bg-red-400">
            <div class="panel-body padding-15-20">
                <div class="clearfix">
                    <div class="pull-left">
                        <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0" data-to="{{ App\Invoice::sum('pending_amount') }}" data-speed="500" data-refresh-interval="10"></div>
                    </div>
                    <div class="pull-right">
                        <i class="font-size-24 color-red-100 fa fa-money"></i>
                    </div>

                    <div class="clearfix"></div>

                    <div class="pull-left">
                        <div class="display-block color-red-50 font-weight-600">Pending Payments</div>
                    </div>
                </div>
            </div>
        </div><!-- /.panel -->
    </div>

    <!-- Collection -->
    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
        <div class="panel bg-green-400">
            <div class="panel-body padding-15-20">
                <div class="clearfix">
                    <div class="pull-left">
                        <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0" data-to="{{ App\PaymentDetail::whereMonth('created_at','=',Carbon::today()->month)->sum('payment_amount') }}" data-speed="500" data-refresh-interval="10"></div>
                    </div>
                    <div class="pull-right">
                        <i class="font-size-24 color-green-100 fa fa-inr"></i>
                    </div>

                    <div class="clearfix"></div>

                    <div class="pull-left">
                        <div class="display-block color-green-50 font-weight-600">Monthly Collection</div>
                    </div>
                </div>
            </div>
        </div><!-- /.panel -->
    </div>
</div>
@endpermission

<!--Member Quick views -->
<div class="row"> <!--Main Row-->
@permission(['manage-gymie','view-dashboard-members-tab'])
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-title">
                <div class="panel-head"><i class="fa fa-users"></i><a href="{{ action('MembersController@index') }}">Members</a></div>
                <div class="pull-right"><a href="{{ action('MembersController@create') }}" class="btn-sm btn-primary active" role="button"><i class="fa fa-user-plus"></i> Add</a></div>
            </div>

            <div class="panel-body with-nav-tabs">
                <!-- Tabs Heads -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#expiring" data-toggle="tab">Expiring<span class="label label-warning margin-left-5">{{ $expiringCount }}</span></a></li>
                    <li><a href="#expired" data-toggle="tab">Expired<span class="label label-danger margin-left-5">{{ $expiredCount }}</span></a></li>
                    <li><a href="#birthdays" data-toggle="tab">Birthdays<span class="label label-success margin-left-5">{{ $birthdayCount }}</span></a></li>
                    <li><a href="#recent" data-toggle="tab">Recent</a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="expiring">
                       	<div class="table-responsive <?php echo (!$expirings->isEmpty() ? "panel-scroll" : "") ?>">
                            <table class="table table-hover table-condensed">
                                @forelse($expirings as $expiring)
                                <tr>
                                    <td>
                                        <?php
                                            $images = $expiring->member->getMedia('profile');
                                            $profileImage = ($images->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=50&h=50' : url($images[0]->getUrl('thumb')));
                                        ?>
                                        <a href="{{ action('MembersController@show',['id' => $expiring->member->id]) }}"><img src="{{ $profileImage }}"/></a>
                                    </td>

                                    <td>
                                        <a href="{{ action('MembersController@show',['id' => $expiring->member->id]) }}"><span class="table-sub-data">{{ $expiring->member->member_code }}</span></a>
                                        <a href="{{ action('MembersController@show',['id' => $expiring->member->id]) }}"><span class="table-sub-data">{{ $expiring->member->name }}</span></a>
                                    </td>
                                    <?php
                                        $daysLeft = Carbon::today()->diffInDays($expiring->end_date->addDays(1));
                                    ?>
                                    <td>
                                        <span class="table-sub-data">{{ $expiring->end_date->format('Y-m-d') }}<br></span>
                                        <span class="table-sub-data">{{ Carbon::today()->addDays($daysLeft)->diffForHumans() }}</span>
                                    </td>

                                     @permission(['manage-gymie','manage-subscriptions','renew-subscription'])
                                    <td>
                                        <a class="btn btn-info btn-xs btn pull-right" href="{{ action('SubscriptionsController@renew',['id' => $expiring->invoice_id]) }}">Renew</a>
                                    </td>
                                    @endpermission
                                </tr>
                                @empty
                                   <div class="tab-empty-panel font-size-24 color-grey-300">
                                        No Data
                                   </div>
                                @endforelse
                            </table>
                        </div>
                        @if(!$expirings->isEmpty())
                        <a class="btn btn-color btn-xs palette-concrete pull-right margin-right-10 margin-top-10" href="{{ action('SubscriptionsController@expiring') }}">View All</a>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="expired">
                            <div class="table-responsive <?php echo (!$allExpired->isEmpty() ? "panel-scroll" : "") ?>
">
                                <table class="table table-hover">
                                    @forelse($allExpired as $expired)
                                    <tr>
                                        <td>
                                            <?php
                                                $images = $expired->member->getMedia('profile');
                                                $profileImage = ($images->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=50&h=50' : url($images[0]->getUrl('thumb')));
                                            ?>
                                            <a href="{{ action('MembersController@show',['id' => $expired->member->id]) }}"><img src="{{ $profileImage }}"/></a>
                                        </td>

                                        <td>
                                            <a href="{{ action('MembersController@show',['id' => $expired->member->id]) }}"><span class="table-sub-data">{{ $expired->member->member_code }}</span></a>
                                            <a href="{{ action('MembersController@show',['id' => $expired->member->id]) }}"><span class="table-sub-data">{{ $expired->member->name }}</span></a>
                                        </td>
                                        <?php
                                            $daysGone = Carbon::today()->diffInDays($expired->end_date);
                                        ?>
                                        <td>
                                            <span class="table-sub-data">{{ $expired->end_date->format('Y-m-d') }}<br></span>
                                            <span class="table-sub-data">{{ Carbon::today()->subDays($daysGone)->diffForHumans() }}</span>
                                        </td>

                                        <td>
                                            {!! Form::Open(['method' => 'POST','action' => ['SubscriptionsController@cancelSubscription',$expired->id]]) !!}
                                            @permission(['manage-gymie','manage-subscriptions','cancel-subscription'])
                                            <button class="btn btn-xs btn-danger pull-right margin-left-5" type="submit">Cancel</button>
                                            @endpermission

                                            @permission(['manage-gymie','manage-subscriptions','renew-subscription'])
                                            <a class="btn btn-xs btn-info pull-right" href="{{ action('SubscriptionsController@renew',['id' => $expired->invoice_id]) }}">Renew</a>
                                            @endpermission
                                            {!! Form::Close() !!}
                                        </td>
                                    </tr>
                                    @empty
                                       <div class="tab-empty-panel font-size-24 color-grey-300">
                                            No Data
                                       </div>
                                    @endforelse
                                </table>
                            </div>
                        @if(!$allExpired->isEmpty())
                        <a class="btn btn-color btn-xs palette-concrete pull-right margin-right-10 margin-top-10" href="{{ action('SubscriptionsController@expired') }}">View All</a>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="birthdays">
                            <div class="table-responsive <?php echo (!$birthdays->isEmpty() ? "panel-scroll" : "") ?>">
                                <table class="table table-hover">
                                    @forelse($birthdays as $birthday)
                                    <tr>
                                        <?php
                                            $images = $birthday->getMedia('profile');
                                            $profileImage = ($images->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=50&h=50' : url($images[0]->getUrl('thumb')));
                                        ?>
                                        <td><a href="{{ action('MembersController@show',['id' => $birthday->id]) }}"><img src="{{ $profileImage }}"/></a></td>
                                        <td><a href="{{ action('MembersController@show',['id' => $birthday->id]) }}">{{ $birthday->name }}</a></td>
                                        <td>{{ $birthday->contact }}</td>
                                        <td>{{ $birthday->DOB->toFormattedDateString() }}</td>
                                    </tr>
                                    @empty
                                       <div class="tab-empty-panel font-size-24 color-grey-300">
                                            No Data
                                       </div>
                                    @endforelse
                                </table>
                          </div>
                    </div>

                    <div class="tab-pane fade" id="recent">
                        <div class="table-responsive <?php echo (!$recents->isEmpty() ? "panel-scroll" : "") ?>">
                            <table class="table table-hover table-condensed">
                                @forelse($recents as $recent)
                                <tr>
                                    <td>
                                        <?php
                                            $images = $recent->getMedia('profile');
                                            $profileImage = ($images->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=50&h=50' : url($images[0]->getUrl('thumb')));
                                        ?>
                                        <a href="{{ action('MembersController@show',['id' => $recent->id]) }}"><img src="{{ $profileImage }}"/></a>
                                    </td>

                                    <td>
                                        <a href="{{ action('MembersController@show',['id' => $recent->id]) }}"><span class="table-sub-data">{{ $recent->member_code }}</span></a>
                                        <a href="{{ action('MembersController@show',['id' => $recent->id]) }}"><span class="table-sub-data">{{ $recent->name }}</span></a>
                                    </td>

                                    <td>
                                        <?php
                                            $daysGone = Carbon::today()->diffInDays($recent->created_at);
                                        ?>
                                        <span class="table-sub-data">{{ $recent->created_at->format('Y-m-d') }}<br></span>
                                        <span class="table-sub-data">{{ Carbon::today()->subDays($daysGone)->diffForHumans() }}</span>
                                    </td>
                                </tr>
                                @empty
                                   <div class="tab-empty-panel font-size-24 color-grey-300">
                                        No Data
                                   </div>
                                @endforelse
                            </table>
                        </div>
                        @if(!$recents->isEmpty())
                        <a class="btn btn-color btn-xs palette-concrete pull-right margin-right-10 margin-top-10" href="{{ action('MembersController@index') }}">View All</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpermission

@permission(['manage-gymie','view-dashboard-enquiries-tab'])
<!--Enquiry Quick view Tabs-->
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-title">
                <div class="panel-head"><i class="fa fa-phone"></i><a href="{{ action('EnquiriesController@index') }}">Enquiries</a></div>
                <div class="pull-right"><a href="{{ action('EnquiriesController@create') }}" class="btn-sm btn-primary active" role="button"><i class="fa fa-phone"></i> Add</a></div>
            </div>

            <div class="panel-body with-nav-tabs">
                <!-- Tabs Heads -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#enquiries" data-toggle="tab">Enquiries</a></li>
                    <li><a href="#reminders" data-toggle="tab">Reminders<span class="label label-warning margin-left-5">{{ $reminderCount }}</span></a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="enquiries">
                        <div class="table-responsive <?php echo (!$enquiries->isEmpty() ? "panel-scroll" : "") ?>">
                            <table class="table table-hover table-condensed">
                                @forelse($enquiries as $enquiry)
                                <tr>
                                    <td><i class="fa fa-user color-blue-grey-100 fa-lg"></i></td>
                                    <td><a href="{{ action('EnquiriesController@show',['id' => $enquiry->id]) }}">{{ $enquiry->name }}</a></td>
                                    <td><a href="{{ action('EnquiriesController@show',['id' => $enquiry->id]) }}">{{ $enquiry->email }}</a></td>
                                    <td><a href="{{ action('EnquiriesController@show',['id' => $enquiry->id]) }}">{{ $enquiry->contact }}</a></td>
                                </tr>
                                @empty
                                   <div class="tab-empty-panel font-size-24 color-grey-300">
                                        No Data
                                   </div>
                                @endforelse
                            </table>
                        </div>

                        @if(!$enquiries->isEmpty())
                        <a class="btn btn-color btn-xs palette-concrete pull-right margin-right-10 margin-top-10" href="{{ action('EnquiriesController@index') }}">View All</a>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="reminders">
                            <div class="table-responsive <?php echo (!$reminders->isEmpty() ? "panel-scroll" : "") ?>">
                                <table class="table table-hover">
                                    @forelse($reminders as $reminder)
                                    <tr>
                                        <td><a href="{{ action('EnquiriesController@show',['id' => $reminder->enquiry->id]) }}">{{ $reminder->enquiry->name }}</a></td>
                                        <td><a href="{{ action('EnquiriesController@show',['id' => $reminder->enquiry->id]) }}">{{ $reminder->enquiry->contact }}</a></td>
                                        <td><a href="{{ action('EnquiriesController@show',['id' => $reminder->enquiry->id]) }}">{{ $reminder->due_date->format('Y-m-d') }}</a></td>
                                        <td><a href="{{ action('EnquiriesController@show',['id' => $reminder->enquiry->id]) }}">{{ Utilities::getFollowupBy ($reminder->followup_by) }}</a></td>
                                    </tr>
                                    @empty
                                       <div class="tab-empty-panel font-size-24 color-grey-300">
                                            No Data
                                       </div>
                                    @endforelse
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpermission
</div> <!--/Main row -->





@permission(['manage-gymie','view-dashboard-expense-tab'])
<div class="row">
<!--Expense Quick view Tabs-->
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-title">
                <div class="panel-head"><i class="fa fa-inr"></i><a href="{{ action('ExpensesController@index') }}">Expenses</a></div>
                <div class="pull-right"><a href="{{ action('ExpensesController@create') }}" class="btn-sm btn-primary active" role="button"><i class="fa fa-inr"></i> Add</a></div>
            </div>

            <div class="panel-body with-nav-tabs">
                <!-- Tabs Heads -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#due" data-toggle="tab">Due</a></li>
                    <li><a href="#outstanding" data-toggle="tab">Outstanding</a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="due">
                        <div class="table-responsive <?php echo (!$dues->isEmpty() ? "panel-scroll" : "") ?>">
                            <table class="table table-hover table-condensed">
                                @forelse($dues as $due)
                                <tr>
                                    <td><a href="{{ action('ExpensesController@edit',['id' => $due->id]) }}">{{ $due->name }}</a></td>
                                    <td><a href="{{ action('ExpensesController@edit',['id' => $due->id]) }}">{{ $due->amount }}</a></td>
                                    <td><a href="{{ action('ExpensesController@edit',['id' => $due->id]) }}">{{ $due->due_date->format('Y-m-d') }}</a></td>
                                    <td><a class="btn btn-info btn-xs btn pull-right" href="{{ action('ExpensesController@paid',['id' => $due->id]) }}">Pay</a></td>
                                </tr>
                                @empty
                                       <div class="tab-empty-panel font-size-24 color-grey-300">
                                            No Data
                                       </div>
                                @endforelse
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="outstanding">
                            <div class="table-responsive <?php echo (!$outstandings->isEmpty() ? "panel-scroll" : "") ?>">
                                <table class="table table-hover">
                                    @forelse($outstandings as $outstanding)
                                    <tr>
                                        <td><a href="{{ action('ExpensesController@edit',['id' => $outstanding->id]) }}">{{ $outstanding->name }}</a></td>
                                        <td><a href="{{ action('ExpensesController@edit',['id' => $outstanding->id]) }}">{{ $outstanding->amount }}</a></td>
                                        <td><a href="{{ action('ExpensesController@edit',['id' => $outstanding->id]) }}">{{ $outstanding->due_date->format('Y-m-d') }}</a></td>
                                        <td><a class="btn btn-info btn-xs btn pull-right" href="{{ action('ExpensesController@paid',['id' => $outstanding->id]) }}">Pay</a></td>
                                    </tr>
                                    @empty
                                       <div class="tab-empty-panel font-size-24 color-grey-300">
                                            No Data
                                       </div>
                                    @endforelse
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpermission

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-title">
                <div class="panel-head"><i class="fa fa-credit-card-alt" aria-hidden="true"></i>Cheques</div>
            </div>

            <div class="panel-body with-nav-tabs">
                <!-- Tabs Heads -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#recieved" data-toggle="tab">Recieved<span class="label label-warning margin-left-5">{{ $recievedChequesCount }}</span></a></li>
                    <li><a href="#deposited" data-toggle="tab">Deposited<span class="label label-primary margin-left-5">{{ $depositedChequesCount }}</span></a></li>
                    <li><a href="#bounced" data-toggle="tab">Bounced<span class="label label-danger margin-left-5">{{ $bouncedChequesCount }}</span></a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="recieved">
                        <div class="table-responsive <?php echo (!$recievedCheques->isEmpty() ? "panel-scroll" : "") ?>">
                            <table class="table table-hover table-condensed">
                                @forelse($recievedCheques as $recievedCheque)
                                <tr>
                                    <td>{{ $recievedCheque->number }}</td>
                                    <td>{{ $recievedCheque->date }}</td>
                                    <td>{{ $recievedCheque->payment->payment_amount }}</td>
                                    <td><a class="btn btn-info btn-xs btn pull-right" href="{{ action('PaymentsController@depositCheque',['id' => $recievedCheque->payment_id]) }}">Mark Deposited</a></td>
                                </tr>
                                @empty
                                       <div class="tab-empty-panel font-size-24 color-grey-300">
                                            No Data
                                       </div>
                                @endforelse
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="deposited">
                            <div class="table-responsive <?php echo (!$depositedCheques->isEmpty() ? "panel-scroll" : "") ?>">
                                <table class="table table-hover">
                                    @forelse($depositedCheques as $depositedCheque)
                                    <tr>
                                        <td>{{ $depositedCheque->number }}</td>
                                        <td>{{ $depositedCheque->date }}</td>
                                        <td>{{ $depositedCheque->payment->payment_amount }}</td>
                                        <td>
                                            <a href="{{ action('PaymentsController@chequeBounce',['id' => $depositedCheque->payment_id]) }}" class="btn btn-xs btn-danger pull-right margin-left-5">Mark Bounced</a>
                                            <a class="btn btn-xs btn-success pull-right" href="{{ action('PaymentsController@clearCheque',['id' => $depositedCheque->payment_id]) }}">Mark Cleared</a>
                                        </td>
                                    </tr>
                                @empty
                                       <div class="tab-empty-panel font-size-24 color-grey-300">
                                            No Data
                                       </div>
                                @endforelse
                                </table>
                            </div>
                    </div>

                    <div class="tab-pane fade" id="bounced">
                        <div class="table-responsive <?php echo (!$bouncedCheques->isEmpty() ? "panel-scroll" : "") ?>">
                            <table class="table table-hover">
                                @forelse($bouncedCheques as $bouncedCheque)
                                <tr>
                                    <td>{{ $bouncedCheque->number }}</td>
                                    <td>{{ $bouncedCheque->date }}</td>
                                    <td>{{ $bouncedCheque->payment->payment_amount }}</td>
                                    <td><a class="btn btn-info btn-xs btn pull-right" href="{{ action('PaymentsController@chequeReissue',['id' => $bouncedCheque->payment_id]) }}">Reissued</a></td>
                                </tr>
                                @empty
                                       <div class="tab-empty-panel font-size-24 color-grey-300">
                                            No Data
                                       </div>
                                @endforelse
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@permission(['manage-gymie','view-dashboard-charts'])
<div class="row">
    <div class="col-lg-6">
            <div class="panel">
                <div class="panel-title">
                    <div class="panel-head"><i class="fa fa-comments-o"></i>SMS Log</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="panel bg-light-blue-400">
                                <div class="panel-body padding-15-20">
                                    <div class="clearfix">
                                        <div class="pull-left">
                                            <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0" data-to="{{ \Utilities::getSetting('sms_balance') }}" data-speed="500" data-refresh-interval="10"></div>
                                        </div>

                                        <div class="pull-right">
                                            <i class="font-size-24 color-light-blue-100 fa fa-comments"></i>
                                        </div>

                                        <div class="clearfix"></div>

                                        <div class="pull-left">
                                            <div class="display-block color-light-blue-50 font-weight-600">SMS balance</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($smsRequestSetting == 0)
                        <div class="col-lg-7">
                            <button class="btn btn-labeled btn-success pull-right margin-top-20" data-toggle="modal" data-target="#smsRequestModal" data-id="smsRequestModal"><span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span>Request more sms</button>
                        </div>
                        @endif
                    </div>
                    <div class="table-responsive <?php echo (!$smslogs->isEmpty() ? "panel-scroll-2" : "") ?>">
                        <table class="table table-hover">
                        @forelse($smslogs as $smslog)
                            <tr>
                                <td>{{ $smslog->number }}</td>
                                <td>{{ $smslog->status }}</td>
                            </tr>
                            @empty
                                   <div class="tab-empty-panel sms-empty-panel font-size-24 color-grey-300">
                                        No Data
                                   </div>
                            @endforelse
                        </table>
                  </div>
                  @if(!$smslogs->isEmpty())
                    <a class="btn btn-color btn-xs palette-concrete pull-right margin-right-10 margin-top-10" href="{{ action('SmsController@logIndex') }}">View All</a>
                  @endif
                </div>
            </div>
    </div>

    <div class="col-lg-6">
        <div class="panel bg-white">
            <div class="panel-title">
                <div class="panel-head">Members Per Plan</div>
            </div>
            <div class="panel-body padding-top-10">
                @if(!empty($membersPerPlan))
                <div id="gymie-members-per-plan" class="chart"></div>
                @else
                  <div class="tab-empty-panel font-size-24 color-grey-300">
                  <div id="gymie-members-per-plan" class="chart"></div>
                        No Data
                 </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel bg-white">
            <div class="panel-title bg-transparent no-border">
                <div class="panel-head">Registration Trend</div>
            </div>
            <div class="panel-body no-padding-top">
                <div id="gymie-registrations-trend" class="chart"></div>
            </div>
        </div>
    </div>
</div>
@endpermission

<!-- SMS request confirmation Modal -->
<div id="smsRequestModal" class="modal fade" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">Confirm request new sms pack</h4>
</div>
<div class="modal-body">
{!! Form::Open(['action' => 'DashboardController@smsRequest']) !!}
<div class="row">
    <div class="col-sm-12">
    <div class="form-group">
{!! Form::label('smsCount','Select SMS Pack') !!}
{!! Form::select('smsCount',array('5000' => '5000 sms', '10000' => '10000 sms', '15000' => '15000 sms'),null,['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'smsCount']) !!}
    </div>
    </div>
</div>

</div>
<div class="modal-footer">
<input type="submit" class="btn btn-info" value="Submit" id="smsRequest"/>
{!! Form::Close() !!}
</div>
</div>
</div>
</div>



</div>
</div>
@stop

@section('footer_scripts')
<script src="{{ URL::asset('assets/plugins/morris/raphael-2.1.0.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/morris/morris.min.js') }}" type="text/javascript"></script>
@stop

@section('footer_script_init')
  <script type="text/javascript">
  $(document).ready(function(){
        gymie.loadmorris();
    });
</script>
@stop

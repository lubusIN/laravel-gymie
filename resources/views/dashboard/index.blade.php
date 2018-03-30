@extends('app')

@section('content')

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
                    @include('dashboard._index.totalMembers')
                </div>

                <!-- Registrations This Weeks -->
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    @include('dashboard._index.registersThisWeek')
                </div>

                <!-- Inactive Members -->
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    @include('dashboard._index.inActiveMembers')
                </div>

                <!-- Members Expired -->
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    @include('dashboard._index.expiredMembers')
                </div>

                <!-- Outstanding Payments -->
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    @include('dashboard._index.outstandingPayments')
                </div>

                <!-- Collection -->
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    @include('dashboard._index.collection')
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
                            <div class="pull-right"><a href="{{ action('MembersController@create') }}" class="btn-sm btn-primary active" role="button"><i
                                            class="fa fa-user-plus"></i> Add</a></div>
                        </div>

                        <div class="panel-body with-nav-tabs">
                            <!-- Tabs Heads -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#expiring" data-toggle="tab">Expiring<span
                                                class="label label-warning margin-left-5">{{ $expiringCount }}</span></a></li>
                                <li><a href="#expired" data-toggle="tab">Expired<span class="label label-danger margin-left-5">{{ $expiredCount }}</span></a>
                                </li>
                                <li><a href="#birthdays" data-toggle="tab">Birthdays<span class="label label-success margin-left-5">{{ $birthdayCount }}</span></a>
                                </li>
                                <li><a href="#recent" data-toggle="tab">Recent</a></li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="expiring">
                                    @include('dashboard._index.expiring', ['expirings' => $expirings])
                                </div>

                                <div class="tab-pane fade" id="expired">
                                    @include('dashboard._index.expired', ['allExpired' => $allExpired])
                                </div>

                                <div class="tab-pane fade" id="birthdays">
                                    @include('dashboard._index.birthdays', ['birthdays' => $birthdays])
                                </div>

                                <div class="tab-pane fade" id="recent">
                                    @include('dashboard._index.recents', ['recents' =>  $recents])
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
                            <div class="pull-right"><a href="{{ action('EnquiriesController@create') }}" class="btn-sm btn-primary active" role="button"><i
                                            class="fa fa-phone"></i> Add</a></div>
                        </div>

                        <div class="panel-body with-nav-tabs">
                            <!-- Tabs Heads -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#enquiries" data-toggle="tab">Enquiries</a></li>
                                <li><a href="#reminders" data-toggle="tab">Reminders<span class="label label-warning margin-left-5">{{ $reminderCount }}</span></a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="enquiries">
                                    @include('dashboard._index.enquiries')
                                </div>

                                <div class="tab-pane fade" id="reminders">
                                    @include('dashboard._index.reminders', ['reminders' => $reminders])
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
                            <div class="pull-right"><a href="{{ action('ExpensesController@create') }}" class="btn-sm btn-primary active" role="button">
                                    <i class="fa fa-inr"></i> Add</a>
                            </div>
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
                                    @include('dashboard._index.due', ['dues' => $dues])
                                </div>

                                <div class="tab-pane fade" id="outstanding">
                                    @include('dashboard._index.outStanding', ['outstandings' => $outstandings])
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
                                <li class="active"><a href="#recieved" data-toggle="tab">Recieved<span
                                                class="label label-warning margin-left-5">{{ $recievedChequesCount }}</span></a></li>
                                <li><a href="#deposited" data-toggle="tab">Deposited<span
                                                class="label label-primary margin-left-5">{{ $depositedChequesCount }}</span></a></li>
                                <li><a href="#bounced" data-toggle="tab">Bounced<span class="label label-danger margin-left-5">{{ $bouncedChequesCount }}</span></a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="recieved">
                                    <div class="table-responsive <?php echo(! $recievedCheques->isEmpty() ? "panel-scroll" : "") ?>">
                                        <table class="table table-hover table-condensed">
                                            @forelse($recievedCheques as $recievedCheque)
                                                <tr>
                                                    <td>{{ $recievedCheque->number }}</td>
                                                    <td>{{ $recievedCheque->date }}</td>
                                                    <td>{{ $recievedCheque->payment->payment_amount }}</td>
                                                    <td><a class="btn btn-info btn-xs btn pull-right"
                                                           href="{{ action('PaymentsController@depositCheque',['id' => $recievedCheque->payment_id]) }}">Mark
                                                            Deposited</a></td>
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
                                    @include('dashboard._index.deposited', ['depositedCheques' =>  $depositedCheques])
                                </div>

                                <div class="tab-pane fade" id="bounced">
                                    <div class="table-responsive <?php echo(! $bouncedCheques->isEmpty() ? "panel-scroll" : "") ?>">
                                        <table class="table table-hover">
                                            @forelse($bouncedCheques as $bouncedCheque)
                                                <tr>
                                                    <td>{{ $bouncedCheque->number }}</td>
                                                    <td>{{ $bouncedCheque->date }}</td>
                                                    <td>{{ $bouncedCheque->payment->payment_amount }}</td>
                                                    <td><a class="btn btn-info btn-xs btn pull-right"
                                                           href="{{ action('PaymentsController@chequeReissue',['id' => $bouncedCheque->payment_id]) }}">Reissued</a>
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
                                                    <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0"
                                                         data-from="0" data-to="{{ \Utilities::getSetting('sms_balance') }}" data-speed="500"
                                                         data-refresh-interval="10"></div>
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
                                        <button class="btn btn-labeled btn-success pull-right margin-top-20" data-toggle="modal" data-target="#smsRequestModal"
                                                data-id="smsRequestModal"><span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span>Request more sms
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="table-responsive <?php echo(! $smslogs->isEmpty() ? "panel-scroll-2" : "") ?>">
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
                                <a class="btn btn-color btn-xs palette-concrete pull-right margin-right-10 margin-top-10"
                                   href="{{ action('SmsController@logIndex') }}">View All</a>
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
        $(document).ready(function () {
            gymie.loadmorris();
        });
    </script>
@stop

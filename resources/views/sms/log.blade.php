@extends('app')
@section('content')
    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title">SMS Log
                <small>Complete sms log</small>
            </h1>
        </div>

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border ">
                        <div class="panel-body no-padding-top bg-white">
                            <div class="row margin-top-15 margin-bottom-15">
                                <div class="col-xs-6">
                                    <a href="{{ action('SmsController@logRefresh') }}" class="btn btn-labeled btn-info"><span class="btn-label"><i
                                                    class="glyphicon glyphicon-refresh"></i></span>Refresh</a>
                                </div>
                                <div class="col-xs-6 col-md-3 pull-right">
                                    {!! Form::Open(['method' => 'GET']) !!}
                                    <div class="btn-inline pull-right">
                                        <input name="search" id="search" type="text" class="form-control padding-right-35" placeholder="Search...">
                                        <button class="btn btn-link no-shadow bg-transparent no-padding-top padding-right-10" type="button"><i
                                                    class="ion-search"></i></button>
                                    </div>
                                    {!! Form::Close() !!}
                                </div>
                            </div>

                            @if($smslogs->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else

                                <table id="smslogs" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Number</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Send Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($smslogs as $smslog)
                                        <tr>
                                            <td>{{ $smslog->number }}</td>
                                            <td>{{ urldecode($smslog->message) }}</td>
                                            <td>{{ $smslog->status }}</td>
                                            <td>{{ $smslog->send_time->toDayDateTimeString() }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            Showing page {{ $smslogs->currentPage() }} of {{ $smslogs->lastPage() }}
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">
                                            {!! str_replace('/?', '?', $smslogs->appends(Input::Only('search'))->render()) !!}
                                        </div>
                                    </div>
                                </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@extends('app')

@section('content')

    <div class="rightside bg-grey-100">

        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Enquiries
                @permission(['manage-gymie','manage-enquiries','add-enquiry'])
                <a href="{{ action('EnquiriesController@create') }}" class="page-head-btn btn-sm btn-primary active" role="button">Add New</a>
                <small>Details of all gym enquiries</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right"><span data-toggle="counter" data-start="0"
                                                                                                                     data-from="0" data-to="{{ $count }}"
                                                                                                                     data-speed="600"
                                                                                                                     data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total Enquiries</small>
            </h1>
            @endpermission
            @endpermission
        </div><!-- / PageHead -->

        <div class="container-fluid">

            <div class="row"><!-- Main row -->
                <div class="col-lg-12"><!-- Main col -->
                    <div class="panel no-border">

                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">

                                <div class="row">
                                    <div class="col-sm-12 no-padding">
                                        {!! Form::Open(['method' => 'GET']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::label('enquiry-daterangepicker','Date range') !!}
                                            <div id="enquiry-daterangepicker"
                                                 class="gymie-daterangepicker btn bg-grey-50 daterange-padding no-border color-grey-600 hidden-xs no-shadow">
                                                <i class="ion-calendar margin-right-10"></i>
                                                <span>{{$drp_placeholder}}</span>
                                                <i class="ion-ios-arrow-down margin-left-5"></i>
                                            </div>
                                            {!! Form::text('drp_start',null,['class'=>'hidden', 'id' => 'drp_start']) !!}
                                            {!! Form::text('drp_end',null,['class'=>'hidden', 'id' => 'drp_end']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_field','Sort By') !!}
                                            {!! Form::select('sort_field',array('created_at' => 'Date','name' => 'Name','status' => 'Status'),old('sort_field'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_field']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_direction','Order') !!}
                                            {!! Form::select('sort_direction',array('desc' => 'Descending','asc' => 'Ascending'),old('sort_direction'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_direction']) !!}</span>
                                        </div>

                                        <div class="col-xs-3">
                                            {!! Form::label('search','Keyword') !!}
                                            <input value="{{ old('search') }}" name="search" id="search" type="text" class="form-control padding-right-35"
                                                   placeholder="Search...">
                                        </div>

                                        <div class="col-xs-2">
                                            {!! Form::label('&nbsp;') !!} <br/>
                                            <button type="submit" class="btn btn-primary active no-border">GO</button>
                                        </div>
                                        {!! Form::Close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel-body bg-white">
                            @if($enquiries->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else
                                <table id="enquiries" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Gender</th>
                                        <th>Enquired On</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($enquiries as $enquiry)
                                        <tr>
                                            <td><a href="{{ action('EnquiriesController@show',['id' => $enquiry->id]) }}">{{ $enquiry->name}}</a></td>
                                            <td>{{ $enquiry->contact}}</td>
                                            <td>{{ $enquiry->address}}</td>
                                            <td>{{ Utilities::getGender($enquiry->gender)}}</td>
                                            <td>{{ $enquiry->created_at->format('Y-m-d')}}</td>
                                            <td>
                                                <span class="{{ Utilities::getEnquiryLabel ($enquiry->status) }}">{{ Utilities::getEnquiryStatus($enquiry->status) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Actions</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @permission(['manage-gymie','manage-enquiries','view-enquiry'])
                                                        <li>
                                                            <a href="{{ action('EnquiriesController@show',['id' => $enquiry->id]) }}">
                                                                View details
                                                            </a>
                                                        </li>
                                                        @endpermission

                                                        @permission(['manage-gymie','manage-enquiries','edit-enquiry'])
                                                        <li>
                                                            <a href="{{ action('EnquiriesController@edit',['id' => $enquiry->id]) }}">
                                                                Edit details
                                                            </a>
                                                        </li>
                                                        @endpermission

                                                        @permission(['manage-gymie','manage-enquiries','transfer-enquiry'])
                                                        @if($enquiry->status == 1)
                                                            <li>
                                                                <a href="{{ action('MembersController@transfer',['id' => $enquiry->id]) }}">Transfer to
                                                                    member</a>
                                                            </li>
                                                        @endif
                                                        @endpermission

                                                        @permission(['manage-gymie','manage-enquiries','view-enquiry'])
                                                        @if($enquiry->status == 1)
                                                            <li>
                                                                <a href="#" class="mark-enquiry-as"
                                                                   data-goto-url="{{ url('enquiries/'.$enquiry->id.'/markMember') }}"
                                                                   data-record-id="{{$enquiry->id}}">Mark as member</a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="mark-enquiry-as" data-goto-url="{{ url('enquiries/'.$enquiry->id.'/lost') }}"
                                                                   data-record-id="{{$enquiry->id}}">Mark Lost</a>
                                                            </li>
                                                        @endif
                                                        @endpermission
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                                <div class="row"><!-- Table bottom row -->
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            Showing page {{ $enquiries->currentPage() }} of {{ $enquiries->lastPage() }}
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">
                                            {!! str_replace('/?', '?', $enquiries->appends(Input::Only('search'))->render()) !!}
                                        </div>
                                    </div>
                                </div><!-- / Table bottom row -->

                        </div><!-- / Panel-Body -->
                        @endif
                    </div><!-- / Panel no border -->
                </div><!-- / Main col -->
            </div><!-- / Main row -->
        </div><!-- / Container -->
    </div><!-- / Rightside -->
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.markEnquiryAs();
        });
    </script>
@stop

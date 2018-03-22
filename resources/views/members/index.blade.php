@extends('app')

@section('content')

    <div class="rightside bg-grey-100">

        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Members
                @permission(['manage-gymie','manage-members','add-member'])
                <a href="{{ action('MembersController@create') }}" class="page-head-btn btn-sm btn-primary active" role="button">Add New</a>
                <small>Details of all gym members</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right"><span data-toggle="counter" data-start="0"
                                                                                                                     data-from="0" data-to="{{ $count }}"
                                                                                                                     data-speed="600"
                                                                                                                     data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total Members</small>
            </h1>
            @endpermission
            @endpermission
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <div class="row"><!-- Main row -->
                <div class="col-lg-12"><!-- Main Col -->
                    <div class="panel no-border ">
                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">

                                <div class="row">
                                    <div class="col-sm-12 no-padding">
                                        {!! Form::Open(['method' => 'GET']) !!}

                                        <div class="col-sm-3">

                                            {!! Form::label('member-daterangepicker','Date range') !!}

                                            <div id="member-daterangepicker"
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
                                            {!! Form::select('sort_field',array('created_at' => 'Date','name' => 'Name', 'member_code' => 'Member code', 'status' => 'Status'),old('sort_field'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_field']) !!}
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

                            @if($members->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else
                                <table id="members" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Plan name</th>
                                        <th>Member since</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($members as $member)
                                        <?php
                                        $subscriptions = $member->subscriptions;
                                        $plansArray = array();
                                        foreach ($subscriptions as $subscription) {
                                            $plansArray[] = $subscription->plan->plan_name;
                                        }
                                        $images = $member->getMedia('profile');
                                        $profileImage = ($images->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=50&h=50' : url($images[0]->getUrl('thumb')));
                                        ?>
                                        <tr>
                                            <td><a href="{{ action('MembersController@show',['id' => $member->id]) }}"><img src="{{ $profileImage }}"/></a></td>
                                            <td><a href="{{ action('MembersController@show',['id' => $member->id]) }}">{{ $member->member_code}}</a></td>
                                            <td><a href="{{ action('MembersController@show',['id' => $member->id]) }}">{{ $member->name}}</a></td>
                                            <td>{{ $member->contact}}</td>
                                            <td>{{ implode(",",$plansArray) }}</td>
                                            <td>{{ $member->created_at->format('Y-m-d')}}</td>
                                            <td>
                                                <span class="{{ Utilities::getActiveInactive ($member->status) }}">{{ Utilities::getStatusValue ($member->status) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Actions</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            @permission(['manage-gymie','manage-members','view-member'])
                                                            <a href="{{ action('MembersController@show',['id' => $member->id]) }}">View details</a>
                                                            @endpermission
                                                        </li>
                                                        <li>
                                                            @permission(['manage-gymie','manage-members','edit-member'])
                                                            <a href="{{ action('MembersController@edit',['id' => $member->id]) }}">Edit details</a>
                                                            @endpermission
                                                        </li>
                                                        <li>
                                                            @permission(['manage-gymie','manage-members','delete-member'])
                                                            <a href="#" class="delete-record" data-delete-url="{{ url('members/'.$member->id.'/archive') }}"
                                                               data-record-id="{{$member->id}}">Delete member</a>
                                                            @endpermission
                                                        </li>
                                                    </ul>
                                                </div>


                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            Showing page {{ $members->currentPage() }} of {{ $members->lastPage() }}
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">
                                            {!! str_replace('/?', '?', $members->appends(Input::all())->render()) !!}
                                        </div>
                                    </div>
                                </div>

                        </div><!-- / Panel Body -->
                        @endif
                    </div><!-- / Panel-no-border -->
                </div><!-- / Main Col -->
            </div><!-- / Main Row -->
        </div><!-- / Container -->
    </div><!-- / RightSide -->
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.deleterecord();
        });
    </script>
@stop        

@extends('app')

@section('content')
    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title">Events
                <small>Details of all sms events</small>
            </h1>
            @permission(['manage-gymie','manage-sms','add-sms'])
            <a href="{{ action('SmsController@createEvent') }}" class="btn btn-primary active pull-right" role="button"><i class="ion-compose"></i> Add</a></h1>
            @endpermission
        </div>

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border ">
                        <div class="panel-body no-padding-top bg-white">
                            <div class="row margin-top-15 margin-bottom-15">
                                <div class="col-xs-12 col-md-3 pull-right">
                                    {!! Form::Open(['method' => 'GET']) !!}
                                    <div class="btn-inline pull-right">
                                        <input name="search" id="search" type="text" class="form-control padding-right-35" placeholder="Search...">
                                        <button class="btn btn-link no-shadow bg-transparent no-padding-top padding-right-10" type="button"><i
                                                    class="ion-search"></i></button>
                                    </div>
                                    {!! Form::Close() !!}

                                </div>
                            </div>

                            @if($events->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else

                                <table id="events" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Message</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($events as $event)
                                        <tr>
                                            <td>{{ $event->name}}</td>
                                            <td>{{ $event->date->format('Y-m-d')}}</td>
                                            <td>{{ $event->message}}</td>
                                            <td>{{ $event->description}}</td>
                                            <td>
                                                <span class="{{ Utilities::getActiveInactive ($event->status) }}">{{ Utilities::getStatusValue ($event->status) }}</span>
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
                                                            @permission(['manage-gymie','manage-events','edit-event'])
                                                            <a href="{{ action('SmsController@editEvent',['id' => $event->id]) }}">
                                                                Edit details
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                        <li>
                                                            @permission(['manage-gymie','manage-events','delete-event'])
                                                            <a data-toggle="modal" data-target="#deleteModal-{{$event->id}}" data-id="{{$event->id}}">
                                                                Delete event
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                    </ul>
                                                </div>

                                                <!-- Modal -->
                                                <div id="deleteModal-{{$event->id}}" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">

                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">Confirm</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete it?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                {!! Form::Open(['action'=>['SmsController@destroyEvent',$event->id],'method' => 'POST','id'=>'archiveform-'.$event->id]) !!}
                                                                <input type="submit" class="btn btn-danger" value="Yes" id="btn-{{ $event->id }}"/>
                                                                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                                                {!! Form::Close() !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>


                                </table>

                                <!-- Pagination -->
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            Showing page {{ $events->currentPage() }} of {{ $events->lastPage() }}
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">
                                            {!! str_replace('/?', '?', $events->appends(Input::Only('search'))->render()) !!}
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
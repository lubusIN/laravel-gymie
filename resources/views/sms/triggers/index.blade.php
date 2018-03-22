@extends('app')

@section('content')
    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title">SMS Triggers</h1>
        </div>

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border ">
                        <div class="panel-body no-padding-top bg-white">
                            {!! Form::Open(['method' => 'POST','action' => ['SmsController@triggerUpdate']]) !!}
                            <div class="row margin-top-15 margin-bottom-15">
                                <div class="col-xs-12 col-md-3 pull-right">
                                    {!! Form::submit('Save', ['class' => 'btn btn-sm btn-primary pull-right']) !!}
                                </div>
                            </div>

                            @if($triggers->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No records found</h4>
                            @else

                                <table id="triggers" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Message</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">On/Off</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($triggers as $trigger)
                                        <tr>
                                            <td class="text-center">{{ $trigger->name}}</td>
                                            <td class="text-center">{{ $trigger->message}}</td>
                                            <td class="text-center"><span
                                                        class="{{ Utilities::getActiveInactive ($trigger->status) }}">{{ Utilities::getStatusValue ($trigger->status) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="checkbox checkbox-theme">
                                                    <?php $status = ($trigger->status == 1 ? 'checked="checked"' : '') ?>
                                                    <input type="checkbox" name="triggers[]" id="trigger_{{$trigger->id}}"
                                                           value="{{$trigger->id}}" {{ $status }}>
                                                    <label for="trigger_{{$trigger->id}}"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {!! Form::Close() !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/trigger.js') }}" type="text/javascript"></script>
@stop
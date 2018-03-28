@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the sms event</div>
                        </div>

                        {!! Form::model($event, ['method' => 'POST','action' => ['SmsController@updateEvent',$event->id],'id'=>'smseventsform']) !!}

                        @include('sms.events._form',['submitButtonText' => 'Update'])

                        {!! Form::Close() !!}

                        </form>

                    </div>
                </div>
            </div>
        </div>

        @stop
        @section('footer_scripts')
            <script src="{{ URL::asset('assets/js/event.js') }}" type="text/javascript"></script>
@stop
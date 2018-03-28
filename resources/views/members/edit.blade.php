@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::model($member, ['method' => 'POST','files'=>'true','action' => ['MembersController@update',$member->id],'id'=>'membersform']) !!}


                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the member</div>
                        </div>
                        <div class="panel-body">

                            @include('members.form')
                        </div><!-- / Panel Body -->
                    </div><!-- / Panel-no-border -->

                    <div class="row">
                        <div class="col-sm-2 pull-right">
                            <div class="form-group">
                                {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right']) !!}
                            </div>
                        </div>
                    </div>

                    {!! Form::Close() !!}

                </div><!-- / Main Col -->
            </div><!-- / Main Row -->
        </div><!-- / Container -->
    </div><!-- / Rightside -->

@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/member.js') }}" type="text/javascript"></script>
@stop
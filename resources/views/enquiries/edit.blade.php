@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the enquiry</div>
                        </div>

                        {!! Form::model($enquiry, ['method' => 'POST','files'=>'true','action' => ['EnquiriesController@update',$enquiry->id],'id'=>'enquiriesform']) !!}
                        <div class="panel-body">
                            @include('enquiries.form')
                            <div class="row">
                                <div class="col-sm-2 pull-right">
                                    <div class="form-group">
                                        {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right']) !!}
                                    </div>
                                </div>
                            </div>
                        </div><!-- End of panel body -->

                        {!! Form::Close() !!}

                    </div><!-- / Panel no-border -->
                </div><!-- / Col-md-12 -->
            </div><!-- / row -->
        </div><!-- / container -->
    </div><!-- / rightside -->

@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/enquiry.js') }}" type="text/javascript"></script>
@stop
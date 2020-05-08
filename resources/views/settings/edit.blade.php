@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title">Settings</h1>
        </div>

        <div class="container-fluid">
        {!! Form::Open(['url' => 'settings/save','id'=>'settingsform','files'=>'true']) !!}
        <!-- General Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-cogs"></i> General</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <!--Main row start-->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('gym_name','Gym Name') !!}
                                        {!! Form::text('gym_name',$settings['gym_name'],['class'=>'form-control', 'id' => 'gym_name']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('financial_start','Financial year start') !!}
                                        {!! Form::text('financial_start',$settings['financial_start'],['class'=>'form-control datepicker-default', 'id' => 'financial_start']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('financial_end','Financial year end') !!}
                                        {!! Form::text('financial_end',$settings['financial_end'],['class'=>'form-control datepicker-default', 'id' => 'financial_end']) !!}
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                @if($settings['gym_logo'] != "")
                                    <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {!! Form::label('gym_logo','Gym Logo') !!}<br>
                                                    @if($settings['gym_logo'])
                                                        <img style="width: 100px;" alt="gym logo" src="{{url('assets/img/gym/'. $settings['gym_logo'] ) }}"/>
                                                    @else
                                                        <img alt="gym logo" src="{{url('/images/Invoice/'.'gym_logo'.'.jpg') }}"/>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {!! Form::file('gym_logo',['class'=>'form-control', 'id' => 'gym_logo']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {!! Form::label('gym_logo','Gym Logo') !!}
                                            {!! Form::file('gym_logo',['class'=>'form-control', 'id' => 'gym_logo']) !!}
                                        </div>
                                    </div>
                                @endif

                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {!! Form::label('gym_address_1','Gym Address line 1') !!}
                                                {!! Form::text('gym_address_1',$settings['gym_address_1'],['class'=>'form-control', 'id' => 'gym_address_1']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {!! Form::label('gym_address_2','Gym Address Line 2') !!}
                                                {!! Form::text('gym_address_2',$settings['gym_address_2'],['class'=>'form-control', 'id' => 'gym_address_2']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Invoice Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-file"></i> Invoice</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">                <!--Main row start-->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                {!! Form::label('invoice_prefix','Invoice prefix') !!}
                                                {!! Form::text('invoice_prefix',$settings['invoice_prefix'],['class'=>'form-control', 'id' => 'invoice_prefix']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                {!! Form::label('invoice_last_number','Invoice Last Number') !!}
                                                {!! Form::text('invoice_last_number',$settings['invoice_last_number'],['class'=>'form-control', 'id' => 'invoice_last_number']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                {!! Form::label('invoice_name_type','Invoice name type') !!}
                                                {!! Form::select('invoice_name_type',array('gym_logo' => 'Gym Logo', 'gym_name' => 'Gym Name'),$settings['invoice_name_type'],['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'invoice_name_type']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                {!! Form::label('invoice_number_mode','Invoice number mode') !!}
                                                {!! Form::select('invoice_number_mode',array('0' => 'Manual', '1' => 'Automatic'),$settings['invoice_number_mode'],['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'invoice_number_mode']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- member Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-users"></i> Member</div>
                        </div>

                        <div class="panel-body">
                            <div class="row"><!--Main row start-->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('member_prefix','Member Prefix') !!}
                                                {!! Form::text('member_prefix',$settings['member_prefix'],['class'=>'form-control', 'id' => 'member_prefix']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('member_last_number','Member Last Number') !!}
                                                {!! Form::text('member_last_number',$settings['member_last_number'],['class'=>'form-control', 'id' => 'member_last_number']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('member_number_mode','Member number mode') !!}
                                                {!! Form::select('member_number_mode',array('0' => 'Manual', '1' => 'Automatic'),$settings['member_number_mode'],['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'member_number_mode']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charges Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-dollar"></i> Charges</div>
                        </div>

                        <div class="panel-body">
                            <div class="row"><!--Main row start-->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('admission_fee','Admission Fee') !!}
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                                    {!! Form::text('admission_fee',$settings['admission_fee'],['class'=>'form-control', 'id' => 'admission_fee']) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('taxes','Taxes') !!}
                                                <div class="input-group">
                                                    {!! Form::text('taxes',$settings['taxes'],['class'=>'form-control', 'id' => 'taxes']) !!}
                                                    <div class="input-group-addon"><i class="fa fa-percent"></i></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {!! Form::label('discounts','Discount percent available') !!}
                                                {!! Form::text('discounts',$settings['discounts'],['class'=>'form-control tokenfield', 'id' => 'discounts', 'placeholder' => 'Type discount % and hit enter']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- SMS Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-15"><i class="fa fa-file-text-o"></i> SMS</div>
                        </div>

                        <div class="panel-body">
                            <div class="row"><!--Main row start-->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::label('sms','Turn SMS on?') !!}
                                                {!! Form::select('sms',array('0' => 'No', '1' => 'Yes'),$settings['sms'],['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sms']) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::label('primary_contact','Primary contact') !!}
                                                {!! Form::text('primary_contact',$settings['primary_contact'],['class'=>'form-control', 'id' => 'primary_contact']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @role('Gymie')
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::label('sms_request','SMS Request') !!}
                                                {!! Form::select('sms_request',array('0' => 'Not requested', '1' => 'Requested'),$settings['sms_request'],['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sms_request']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endrole
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Form Submission -->
            <div class="row">
                <div class="col-sm-2 pull-right">
                    <div class="form-group">
                        {!! Form::submit('Save', ['class' => 'btn btn-primary pull-right']) !!}
                    </div>
                </div>
            </div>
            {!! Form::Close() !!}
        </div>
    </div>
@stop

@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/setting.js') }}" type="text/javascript"></script>
@stop

@section('footer_script_init')
    <script type="text/javascript">
        gymie.loadBsTokenInput();
    </script>
@stop
@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title">Settings</h1>
            <a href="{{ action('SettingsController@edit') }}" class="btn btn-primary active pull-right" role="button"><i class="ion-compose"></i> Edit</a></h1>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title bg-white">
                            <div class="panel-head font-size-18"><i class="fa fa-cogs"></i> General</div>
                        </div>

                        <div class="panel-body"> <!-- Panel Body -->

                            <div class="row"> <!-- First Row -->

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Gym Name</label>
                                        <p>{{ $settings['gym_name'] }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Financial year start</label>
                                        <p>{{ $settings['financial_start'] }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Financial year end</label>
                                        <p>{{ $settings['financial_end'] }}</p>
                                    </div>
                                </div>

                            </div>                <!-- / First Row -->

                            <div class="row"> <!-- Second row -->

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <img alt="gym logo" src="{{url('/images/50x50/'.'gym_logo'.'.jpg') }}"/>
                                    </div>
                                </div>

                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Gym Address Line 1</label>
                                                <p>{{ $settings['gym_address_1'] }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Gym Address Line 2</label>
                                                <p>{{ $settings['gym_address_2'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- / Second row -->

                        </div>    <!-- / Panel Body -->

                    </div> <!-- / Panel No border -->
                </div> <!-- / Main Column -->
            </div> <!-- / Main Row -->


            <!--Invoice Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title bg-white">
                            <div class="panel-head font-size-18"><i class="fa fa-file"></i> Invoice</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">        <!-- Panel Row -->
                                <div class="col-sm-12"> <!-- Panel Column -->

                                    <div class="row">   <!-- Inner row -->
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Invoice Prefix</label>
                                                <p>{{ $settings['invoice_prefix'] }}</p>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Invoice Last Number</label>
                                                <p>{{ $settings['invoice_last_number'] }}</p>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Display on invoice</label>
                                                <p>{{ Utilities::getDisplay($settings['invoice_name_type']) }}</p>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Invoice number mode</label>
                                                <p>{{ Utilities::getMode($settings['invoice_number_mode']) }}</p>
                                            </div>
                                        </div>
                                    </div>    <!-- / Inner Row -->

                                </div>    <!-- / Panel Column -->
                            </div>    <!-- / Panel Row -->

                        </div>    <!-- / Panel Body -->

                    </div>    <!-- / Panel No border -->
                </div>    <!-- / Main Column -->
            </div>    <!-- / Main Row -->

            <!-- member Settings -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title bg-white">
                            <div class="panel-head font-size-18"><i class="fa fa-users"></i> Member</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="row">     <!-- Inner row -->
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Member code prefix</label>
                                                <p>{{ $settings['member_prefix'] }}</p>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Member last number</label>
                                                <p>{{ $settings['member_last_number'] }}</p>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Member number mode</label>
                                                <p>{{ Utilities::getMode($settings['member_number_mode']) }}</p>
                                            </div>
                                        </div>
                                    </div>     <!-- / Inner row -->

                                </div>
                            </div>
                        </div>        <!-- / Panel Body -->

                    </div>    <!-- / Panel no border -->
                </div>    <!-- / Main Column -->
            </div>    <!-- / Main Row -->


        </div>    <!-- / Container Fluid -->
    </div>    <!-- / RightSide -->
@stop
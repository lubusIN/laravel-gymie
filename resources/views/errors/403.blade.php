@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="wrapper">
                <div class="panel no-shadow bg-transparent panel-center">
                    <div class="panel-body no-padding">
                        <h1 class="font-size-70 margin-top-30 margin-bottom-10 color-blue-grey-600 animated fadeInDown">
                            <span data-toggle="counter"
                                  data-start="0" data-from="0"
                                  data-to="403" data-speed="600"
                                  data-refresh-interval="10"></span>
                            <small class="color-blue-grey-600 display-block margin-top-5 font-size-20">Forbidden Access</small>
                        </h1>
                        <p class="padding-30 color-grey-700 animated fadeInLeft">We're sorry but you dont have the required permissions<br/>You are not
                            authorised to access this resource.</p>

                    </div>
                    <div class="panel-footer padding-md bg-transparent animated fadeInUp">
                        <a href="{{ action('DashboardController@index') }}" class="btn btn-dark bg-light-green-500 color-white margin-right-15">
                            <span class="glyphicon glyphicon-home margin-right-10"></span> Back to home </a>
                        <a href="http://www.lubus.in" class="btn btn-dark bg-light-blue-600 color-white margin-right-10">
                            <span class="glyphicon glyphicon-envelope margin-right-10"></span> Contact Support </a>
                    </div>
                </div><!-- /.panel -->
            </div><!-- /.wrapper -->
        </div>
    </div>

@stop
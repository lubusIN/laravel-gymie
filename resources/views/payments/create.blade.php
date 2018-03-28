@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the payment</div>
                        </div>

                        {!! Form::Open(['url' => 'payments','id' => 'paymentsform']) !!}

                        @include('payments.form',['submitButtonText' => 'Accept Payment'])

                        {!! Form::Close() !!}

                        </form>

                    </div>
                </div>
            </div>
        </div>


        @stop

        @section('footer_scripts')
            <script src="{{ URL::asset('assets/js/payment.js') }}" type="text/javascript"></script>
        @stop

        @section('footer_script_init')
            <script type="text/javascript">
                $(document).ready(function () {
                    gymie.loaddatepicker();
                    gymie.chequedetails();
                });
            </script>
@stop
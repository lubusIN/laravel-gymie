@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Enter details of the expense</div>
                        </div>
                        <div class="panel-body">
                            {!! Form::Open(['url' => 'expenses', 'id' => 'expensesform']) !!}

                            @include('expenses.form',['submitButtonText' => 'Add'])

                            {!! Form::Close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/expense.js') }}" type="text/javascript"></script>
@stop
<?php use Carbon\Carbon; ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('name','Name') !!}
            {!! Form::text('name',null,['class'=>'form-control','id'=>'name']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php $expenseCategories = App\ExpenseCategory::where('status', '=', '1')->lists('name', 'id'); ?>
            {!! Form::label('category_id','Category') !!}
            {!! Form::select('category_id',$expenseCategories,null,['class'=>'form-control selectpicker show-tick show-menu-arrow','id'=>'category_id','data-live-search'=> 'true']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('due_date','Due date / Payment date') !!}
            {!! Form::text('due_date',(isset($expense->due_date) ? $expense->due_date->format('Y-m-d') : Carbon::today()->format('Y-m-d')),['class'=>'form-control datepicker-default','id'=>'due_date']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
        {!! Form::label('repeat','Repeat') !!}
        <!--0 for inactive , 1 for active-->
            {!! Form::select('repeat',array('0' => 'Never repeat', '1' => 'Every Day', '2' => 'Every Week', '3' => 'Every Month', '4' => 'Every Year'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'repeat']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('note','Note') !!}
            {!! Form::text('note',null,['class'=>'form-control','id'=>'note']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('amount','Amount') !!}
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                {!! Form::text('amount',null,['class'=>'form-control','id'=>'amount']) !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
</div>
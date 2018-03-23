<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('followup_by','FollowUp By') !!}
            {!! Form::select('followup_by',array('0' => 'Call', '1' => 'SMS', '2' => 'Personal'),null,['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'followup_by']) !!}
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('due_date','Due Date') !!}
            {!! Form::text('due_date',null,['class'=>'form-control datepicker-default', 'id' => 'due_date']) !!}
        </div>
    </div>
</div>
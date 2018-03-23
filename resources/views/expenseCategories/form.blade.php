<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('name','Category Name') !!}
            {!! Form::text('name',null,['class'=>'form-control', 'id' => 'name']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
        {!! Form::label('status','Status') !!}
        <!--0 for inactive , 1 for active-->
            {!! Form::select('status',array('1' => 'Active', '0' => 'InActive'),null,['class' => 'form-control', 'id' => 'status']) !!}
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
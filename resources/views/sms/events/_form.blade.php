<?php
$count = collect(array_filter(explode(',', \Utilities::getSetting('sender_id_list'))))->count();
$senderIds = explode(',', \Utilities::getSetting('sender_id_list'));
?>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('name','Event name') !!}
                {!! Form::text('name',null,['class'=>'form-control', 'id' => 'name']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('date','Event date') !!}
                @if(isset($event) && $event->date != "")
                    {!! Form::text('date',$event->date->format('Y-m-d'),['class'=>'form-control datepicker-default', 'id' => 'date']) !!}
                @else
                    {!! Form::text('date',null,['class'=>'form-control datepicker-default', 'id' => 'date']) !!}
                @endif
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('description','Event description') !!}
                {!! Form::text('description',null,['class'=>'form-control', 'id' => 'description']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
            {!! Form::label('status','Status') !!}
            <!--0 for inactive , 1 for active-->
                {!! Form::select('status',array('1' => 'Active', '0' => 'InActive'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'status']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('send_to','Send to') !!}
                {!! Form::select('send_to[]',array('0' => 'Active members', '1' => 'Inactive members', '2' => 'Lead enquiries', '3' => 'Lost enquiries'),null,['class'=>'form-control selectpicker show-tick show-menu-arrow','multiple' => 'multiple', 'id' => 'send_to']) !!}
            </div>
        </div>
    </div>

    @if($count == 1)

        {!! Form::hidden('sender_id',\Utilities::getSetting('sms_sender_id')) !!}

    @elseif($count > 1)

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="sender_id">Sender Id</label>
                    <select id="sender_id" name="sender_id" class="form-control selectpicker show-tick">
                        @foreach($senderIds as $senderId)
                            <option value="{{ $senderId }}">{{ $senderId }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

    @endif

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('message','Message text') !!}
                {!! Form::textarea('message',null,['class'=>'form-control', 'id' => 'message','rows' => '5']) !!}
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
</div>
                            
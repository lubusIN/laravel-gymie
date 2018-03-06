<div class="row">
	<div class="col-sm-12">
		<div class="form-group">
			{!! Form::label('name','Name',['class'=>'control-label']) !!}
			{!! Form::text('name',null,['class'=>'form-control', 'id' => 'name']) !!}		
		</div>
	</div>
</div>


<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('contact','Contact') !!}
			{!! Form::text('contact',null,['class'=>'form-control', 'id' => 'contact']) !!}
		</div>								
	</div>	

	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('email','Email') !!}
			{!! Form::text('email',null,['class'=>'form-control', 'id' => 'email']) !!}		
		</div>
	</div>							
</div>


<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('DOB','Date Of Birth') !!}
			{!! Form::text('DOB',null,['class'=>'form-control datepicker-default', 'id' => 'DOB']) !!}		
		</div>
	</div>

	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('gender','Gender') !!}
			{!! Form::select('gender',array('m' => 'Male', 'f' => 'Female'),null,['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'gender']) !!}											
		</div>
	</div>
</div>


<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('occupation','Occupation') !!}
{!! Form::select('occupation',array('0' => 'Student', '1' => 'Housewife','2' => 'Self Employed','3' => 'Professional','4' => 'Freelancer','5' => 'Others'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'occupation']) !!}											
		</div>
	</div>

	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('start_by','Start By') !!}
			{!! Form::text('start_by',null,['class'=>'form-control datepicker-default', 'id' => 'start_by']) !!}											
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			<?php $services = App\Service::lists('name', 'id'); ?>
			{!! Form::label('interested_in','Interested In') !!}
			{!! Form::select('interested_in[]',$services,1,['class'=>'form-control selectpicker show-tick show-menu-arrow','multiple' => 'multiple','id' => 'interested_in']) !!}		
		</div>
	</div>

	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('aim','Why do you plan to join?',['class'=>'control-label']) !!}
			{!! Form::select('aim',array('0' => 'Fitness', '1' => 'Networking', '2' => 'Body Building', '3' => 'Fatloss', '4' => 'Weightgain', '5' => 'Others'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'aim']) !!}		
		</div>
	</div>
</div>


<div class="row">
	<div class="col-sm-6">
	<div class="row">
	<div class="col-sm-12">
		<div class="form-group">
			{!! Form::label('source','How do you came to know about us?',['class'=>'control-label']) !!}
			{!! Form::select('source',array('0' => 'Promotions', '1' => 'Word Of Mouth', '2' => 'Others'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'source']) !!}
		</div>	
	</div>	
	<div class="col-sm-12">
		<div class="form-group">
			{!! Form::label('pin_code','Pin Code',['class'=>'control-label']) !!}
			{!! Form::text('pin_code',null,['class'=>'form-control', 'id' => 'pin_code']) !!}		
		</div>
	</div>						
	</div>
	</div>

	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('address','Address') !!}
			{!! Form::textarea('address',null,['class'=>'form-control', 'id' => 'address', 'rows' => 5]) !!}		
		</div>
	</div>								
</div>

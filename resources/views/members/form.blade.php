<?php use Carbon\Carbon; ?>

<!-- Hidden Fields -->
@if(Request::is('members/create'))
{!! Form::hidden('invoiceCounter',$invoiceCounter) !!}
{!! Form::hidden('memberCounter',$memberCounter) !!}
@endif
								 
<div class="row">
	<div class="col-sm-6">
	<div class="form-group">
{!! Form::label('member_code','Member code') !!}
{!! Form::text('member_code',$member_code,['class'=>'form-control', 'id' => 'member_code', ($member_number_mode == \constNumberingMode::Auto ? 'readonly' : '')]) !!}		
	</div>
	</div>

	<div class="col-sm-6">
	<div class="form-group">
{!! Form::label('name','Name',['class'=>'control-label']) !!}
{!! Form::text('name',null,['class'=>'form-control', 'id' => 'name']) !!}		
	</div>
	</div>
</div>	

<div class="row">
	
    <div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('DOB','Date of birth') !!}
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
    {!! Form::label('emergency_contact','Emergency contact') !!}
{!! Form::text('emergency_contact',null,['class'=>'form-control', 'id' => 'emergency_contact']) !!}											
	</div>
	</div>
	<div class="col-sm-6">
    <div class="form-group">
    {!! Form::label('health_issues','Health issues') !!}
{!! Form::text('health_issues',null,['class'=>'form-control', 'id' => 'health_issues']) !!}
    </div>								
    </div>								
</div>

<div class="row">
	<div class="col-sm-6">
	<div class="form-group">
{!! Form::label('proof_name','Proof name') !!}
{!! Form::text('proof_name',null,['class'=>'form-control', 'id' => 'proof_name']) !!}		
	</div>
	</div>

	@if(isset($member))
	<?php
        $media = $member->getMedia('proof');
        $image = ($media->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=70&h=70' : url($media[0]->getUrl('form')));
    ?>
    <div class="col-sm-4">
	<div class="form-group">
{!! Form::label('proof_photo','Proof photo') !!}
{!! Form::file('proof_photo',['class'=>'form-control', 'id' => 'proof_photo']) !!}	
    </div>								
    </div>
    <div class="col-sm-2">
	<img alt="proof Pic" class="pull-right" src="{{ $image }}"/>
    </div>
	@else
	<div class="col-sm-6">
	<div class="form-group">
{!! Form::label('proof_photo','Proof photo') !!}
{!! Form::file('proof_photo',['class'=>'form-control', 'id' => 'proof_photo']) !!}	
    </div>								
    </div>
    @endif								
</div>

<div class="row">
@if(isset($member))
<?php
    $media = $member->getMedia('profile');
    $image = ($media->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=70&h=70' : url($media[0]->getUrl('form')));
?>
    <div class="col-sm-4">
	<div class="form-group">
{!! Form::label('photo','Photo') !!}
{!! Form::file('photo',['class'=>'form-control', 'id' => 'photo']) !!}	
    </div>								
    </div>
    <div class="col-sm-2">
	<img alt="profile Pic" class="pull-right" src="{{ $image }}"/>
    </div>
	@else
	<div class="col-sm-6">
	<div class="form-group">
{!! Form::label('photo','Photo') !!}
{!! Form::file('photo',['class'=>'form-control', 'id' => 'photo']) !!}	
    </div>								
    </div>
    @endif

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
{!! Form::label('aim','Why do you plan to join?',['class'=>'control-label']) !!}
{!! Form::select('aim',array('0' => 'Fitness', '1' => 'Networking', '2' => 'Body Building', '3' => 'Fatloss', '4' => 'Weightgain', '5' => 'Others'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'aim']) !!}		
	</div>
	</div>
	<div class="col-sm-6">
	<div class="form-group">
{!! Form::label('source','How do you came to know about us?',['class'=>'control-label']) !!}
{!! Form::select('source',array('0' => 'Promotions', '1' => 'Word Of Mouth', '2' => 'Others'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'source']) !!}		
	</div>
	</div>
</div>

<div class="row">	
	<div class="col-sm-6">
	<div class="row">
	<div class="col-sm-12">
    <div class="form-group">
{!! Form::label('occupation','Occupation') !!}
{!! Form::select('occupation',array('0' => 'Student', '1' => 'Housewife','2' => 'Self Employed','3' => 'Professional','4' => 'Freelancer','5' => 'Others'),null,['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'occupation']) !!}											
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
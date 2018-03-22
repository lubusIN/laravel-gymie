@extends('app')
@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {!! Form::Open(['method' => 'POST','id' => 'usersform','files'=>'true','action' => ['AclController@updateUser',$user->id]]) !!}

                    <div class="panel no-border">
                        <div class="panel-title bg-white no-border">
                            <div class="panel-head">Enter Details of the user</div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('name','Name') !!}
                                        {!! Form::text('name',$user->name,['class'=>'form-control', 'id' => 'name']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('email','Email') !!}
                                        {!! Form::text('email',$user->email,['class'=>'form-control', 'id' => 'email']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                    {!! Form::label('status','Status') !!}
                                    <!--0 for inactive , 1 for active-->
                                        {!! Form::select('status',array('1' => 'Active', '0' => 'InActive'),$user->status,['class' => 'form-control', 'id' => 'status']) !!}
                                    </div>
                                </div>

                                @if(isset($user))
                                    <?php
                                    $media = $user->getMedia('staff');
                                    $image = ($media->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=70&h=70' : url($media[0]->getUrl('form')));
                                    ?>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {!! Form::label('photo','Photo') !!}
                                            {!! Form::file('photo',['class'=>'form-control', 'id' => 'photo']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <img alt="staff photo" class="pull-right" src="{{ $image }}"/>
                                    </div>
                                @else
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::label('photo','Photo') !!}
                                            {!! Form::file('photo',['class'=>'form-control', 'id' => 'photo']) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('password','Password') !!}
                                        {!! Form::password('password',['class'=>'form-control', 'id' => 'password']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('password_confirmation','Confirm Password') !!}
                                        {!! Form::password('password_confirmation',['class'=>'form-control', 'id' => 'password_confirmation']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel no-border">
                        <div class="panel-title bg-white no-border">
                            <div class="panel-head">Enter Role of the user</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <?php
                                        $withoutGymie = App\Role::where('name', '!=', 'Gymie')->lists('name', 'id');
                                        $withGymie = App\Role::lists('name', 'id');
                                        ?>
                                        {!! Form::label('Role') !!}
                                        {!! Form::select('role_id',(Auth::User()->hasRole('Gymie') ? $withGymie : $withoutGymie),$user->role_user->role_id,['class'=>'form-control selectpicker show-tick', 'id' => 'role_id']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2 pull-right">
                            <div class="form-group">
                                {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right']) !!}
                            </div>
                        </div>
                    </div>
                    {!! Form::Close() !!}
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
    <script src="{{ URL::asset('assets/js/userUpdate.js') }}" type="text/javascript"></script>
@stop
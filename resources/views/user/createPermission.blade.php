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
                    
                            <div class="panel no-border">
                            <div class="panel-title bg-white no-border">
                            <div class="panel-head">Enter Details of the permission</div>
                            </div>

                            {!! Form::Open(['url' => 'permission','id' => 'permissionsform','files'=>'true']) !!}

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                        <div class="form-group">
                                    {!! Form::label('name','Name') !!}
                                    {!! Form::text('name',null,['class'=>'form-control', 'id' => 'name']) !!}       
                                        </div>                          
                                        </div>

                                        <div class="col-sm-3">
                                        <div class="form-group">
                                    {!! Form::label('display_name','Display Name') !!}
                                    {!! Form::text('display_name',null,['class'=>'form-control', 'id' => 'display_name']) !!}       
                                        </div>                          
                                        </div>                          

                                        <div class="col-sm-3">
                                        <div class="form-group">
                                    {!! Form::label('description','Description') !!}
                                    {!! Form::text('description',null,['class'=>'form-control', 'id' => 'description']) !!}     
                                        </div>                          
                                        </div>

                                        <div class="col-sm-3">
                                        <div class="form-group">
                                    {!! Form::label('group_key','Group key') !!}
                                    {!! Form::text('group_key',null,['class'=>'form-control', 'id' => 'group_key']) !!}     
                                        </div>                          
                                        </div>                              
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-2 pull-right">
                                    <div class="form-group">
                                        {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right']) !!}
                                    </div>
                                </div>
                            </div>

                            {!! Form::Close() !!}


                            </div>
                        </div>
                        </div>
                        </div>
                        </div>

@stop
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
                            <div class="panel-head">Enter Details of the Role</div>
                        </div>

                        {!! Form::Open(['method' => 'POST','id' => 'rolesform','action' => ['AclController@updateRole',$role->id]]) !!}

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('name','Name') !!}
                                        {!! Form::text('name',$role->name,['class'=>'form-control', 'id' => 'name']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('display_name','Display name') !!}
                                        {!! Form::text('display_name',$role->display_name,['class'=>'form-control', 'id' => 'display_name']) !!}
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('description','Description') !!}
                                        {!! Form::text('description',$role->description,['class'=>'form-control', 'id' => 'description']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel no-border">
                        <div class="panel-title bg-white no-border">
                            <div class="panel-head">Select permission</div>
                        </div>
                        <div class="panel-body">
                            @foreach($permissions->groupBy('group_key') as $permission_group)
                                <h5>{{$permission_group->pluck('group_key')->pop()}}</h5>
                                <div class="row">
                                    @foreach($permission_group as $permission)
                                        <?php $status = ($permission_role->contains('permission_id', $permission->id) ? 'checked="checked"' : '') ?>
                                        <div class="col-xs-4">
                                            <div class="checkbox checkbox-theme">
                                                <input type="checkbox" name="permissions[]" id="permission_{{$permission->id}}"
                                                       value="{{$permission->id}}" {{ $status }} >
                                                <label for="permission_{{$permission->id}}">{{ $permission->display_name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
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
    </div>

@stop
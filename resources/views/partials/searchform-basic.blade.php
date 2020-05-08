{!! Form::open(['method' => 'GET']) !!}
    <div class="btn-inline pull-right">
        <input name="search" id="search" type="text" class="form-control padding-right-35" value="{{ Request::input('search') }}" placeholder="Search...">
        <button class="btn btn-link no-shadow bg-transparent no-padding-top padding-right-10" type="submit"> <i class="ion-search"></i></button>
    </div>
{!! Form::close() !!}
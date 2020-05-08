<div class="table-responsive {!! (! $birthdays->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover">
        @forelse($birthdays as $birthday)
            <tr>
                <td>
                    <a href="{{ action('MembersController@show',['id' => $birthday->id]) }}">
                        <img class="profile-sm" src="{{ $birthday->photoProfile }}"/>
                    </a>
                </td>
                <td><a href="{{ action('MembersController@show',['id' => $birthday->id]) }}">{{ $birthday->name }}</a></td>
                <td>{{ $birthday->contact }}</td>
                <td>{{ $birthday->DOB }}</td>
            </tr>
        @empty
            <div class="tab-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>
<div class="table-responsive {!! (! $birthdays->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover">
        @forelse($birthdays as $birthday)
            <tr>
                <?php
                $images = $birthday->getMedia('profile');
                $profileImage = ($images->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=18&txt=NA&w=50&h=50' : url($images[0]->getUrl('thumb')));
                ?>
                <td><a href="{{ action('MembersController@show',['id' => $birthday->id]) }}"><img
                                src="{{ $profileImage }}"/></a></td>
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
<?php
    use Carbon\Carbon;
?>
<div class="table-responsive {!! (! $recents->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover table-condensed">
        @forelse($recents as $recent)
            <tr>
                <td>
                    <a href="{{ action('MembersController@show',['id' => $recent->id]) }}">
                        <img class="profile-sm" src="{{ $recent->photoProfile }}"/>
                    </a>
                </td>

                <td>
                    <a href="{{ action('MembersController@show',['id' => $recent->id]) }}"><span
                                class="table-sub-data">{{ $recent->member_code }}</span></a>
                    <a href="{{ action('MembersController@show',['id' => $recent->id]) }}"><span
                                class="table-sub-data">{{ $recent->name }}</span></a>
                </td>

                <td>
                    <?php
                    $daysGone = Carbon::today()->diffInDays($recent->created_at);
                    ?>
                    <span class="table-sub-data">{{ $recent->created_at->format('Y-m-d') }}<br></span>
                    <span class="table-sub-data">{{ Carbon::today()->subDays($daysGone)->diffForHumans() }}</span>
                </td>
            </tr>
        @empty
            <div class="tab-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>
@if(!$recents->isEmpty())
    <a class="btn btn-color btn-xs palette-concrete pull-right margin-right-10 margin-top-10"
       href="{{ action('MembersController@index') }}">View All</a>
@endif
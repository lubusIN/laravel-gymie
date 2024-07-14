<div class="table-responsive {!! (! $smslogs->isEmpty() ? 'panel-scroll-2' : '')  !!}">
    <table class="table table-hover">
        @forelse($smslogs as $smslog)
            <tr>
                <td>{{ $smslog->number }}</td>
                <td>{{ $smslog->status }}</td>
            </tr>
        @empty
            <div class="tab-empty-panel sms-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>
@if(!$smslogs->isEmpty())
    <a class="btn btn-color btn-xs palette-concrete pull-right margin-right-10 margin-top-10"
       href="{{ action('SmsController@logIndex') }}">View All</a>
@endif
<div class="table-responsive {!! (! $reminders->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover">
        @forelse($reminders as $reminder)
            <tr>
                <td>
                    <a href="{{ action('EnquiriesController@show',['id' => $reminder->enquiry->id]) }}">{{ $reminder->enquiry->name }}</a>
                </td>
                <td>
                    <a href="{{ action('EnquiriesController@show',['id' => $reminder->enquiry->id]) }}">{{ $reminder->enquiry->contact }}</a>
                </td>
                <td>
                    <a href="{{ action('EnquiriesController@show',['id' => $reminder->enquiry->id]) }}">{{ $reminder->due_date->format('Y-m-d') }}</a>
                </td>
                <td>
                    <a href="{{ action('EnquiriesController@show',['id' => $reminder->enquiry->id]) }}">{{ Utilities::getFollowupBy ($reminder->followup_by) }}</a>
                </td>
            </tr>
        @empty
            <div class="tab-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>
                                
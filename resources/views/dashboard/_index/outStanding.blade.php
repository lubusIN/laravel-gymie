
<div class="table-responsive {!! (! $outstandings->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover">
        @forelse($outstandings as $outstanding)
            <tr>
                <td>
                    <a href="{{ action('ExpensesController@edit',['id' => $outstanding->id]) }}">{{ $outstanding->name }}</a>
                </td>
                <td>
                    <a href="{{ action('ExpensesController@edit',['id' => $outstanding->id]) }}">{{ $outstanding->amount }}</a>
                </td>
                <td>
                    <a href="{{ action('ExpensesController@edit',['id' => $outstanding->id]) }}">{{ $outstanding->due_date->format('Y-m-d') }}</a>
                </td>
                <td><a class="btn btn-info btn-xs btn pull-right"
                       href="{{ action('ExpensesController@paid',['id' => $outstanding->id]) }}">Pay</a></td>
            </tr>
        @empty
            <div class="tab-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>
                                
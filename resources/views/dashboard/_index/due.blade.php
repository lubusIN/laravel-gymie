<div class="table-responsive {!! (! $dues->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover table-condensed">
        @forelse($dues as $due)
            <tr>
                <td><a href="{{ action('ExpensesController@edit',['id' => $due->id]) }}">{{ $due->name }}</a></td>
                <td><a href="{{ action('ExpensesController@edit',['id' => $due->id]) }}">{{ $due->amount }}</a></td>
                <td>
                    <a href="{{ action('ExpensesController@edit',['id' => $due->id]) }}">{{ $due->due_date->format('Y-m-d') }}</a>
                </td>
                <td><a class="btn btn-info btn-xs btn pull-right"
                       href="{{ action('ExpensesController@paid',['id' => $due->id]) }}">Pay</a></td>
            </tr>
        @empty
            <div class="tab-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>

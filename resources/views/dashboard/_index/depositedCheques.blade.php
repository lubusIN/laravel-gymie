<div class="table-responsive {!! (! $depositedCheques->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover">
        @forelse($depositedCheques as $depositedCheque)
            <tr>
                <td>{{ $depositedCheque->number }}</td>
                <td>{{ $depositedCheque->date }}</td>
                <td>{{ $depositedCheque->payment->payment_amount }}</td>
                <td>
                    <a href="{{ action('PaymentsController@chequeBounce',['id' => $depositedCheque->payment_id]) }}"
                       class="btn btn-xs btn-danger pull-right margin-left-5">Mark Bounced</a>
                    <a class="btn btn-xs btn-success pull-right"
                       href="{{ action('PaymentsController@clearCheque',['id' => $depositedCheque->payment_id]) }}">Mark
                        Cleared</a>
                </td>
            </tr>
        @empty
            <div class="tab-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>


<div class="table-responsive {!! (! $recievedCheques->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover table-condensed">
        @forelse($recievedCheques as $recievedCheque)
            <tr>
                <td>{{ $recievedCheque->number }}</td>
                <td>{{ $recievedCheque->date }}</td>
                <td>{{ $recievedCheque->payment->payment_amount }}</td>
                <td><a class="btn btn-info btn-xs btn pull-right"
                       href="{{ action('PaymentsController@depositCheque',['id' => $recievedCheque->payment_id]) }}">Mark
                        Deposited</a></td>
            </tr>
        @empty
            <div class="tab-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>

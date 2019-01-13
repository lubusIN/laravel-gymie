<div class="table-responsive {!! (! $bouncedCheques->isEmpty() ? 'panel-scroll' : '')  !!}">
    <table class="table table-hover">
        @forelse($bouncedCheques as $bouncedCheque)
            <tr>
                <td>{{ $bouncedCheque->number }}</td>
                <td>{{ $bouncedCheque->date }}</td>
                <td>{{ $bouncedCheque->payment->payment_amount }}</td>
                <td><a class="btn btn-info btn-xs btn pull-right"
                       href="{{ action('PaymentsController@chequeReissue',['id' => $bouncedCheque->payment_id]) }}">Reissued</a>
                </td>
            </tr>
        @empty
            <div class="tab-empty-panel font-size-24 color-grey-300">
                No Data
            </div>
        @endforelse
    </table>
</div>

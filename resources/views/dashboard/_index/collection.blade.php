<?php
    use Carbon\Carbon;
   $sum = App\PaymentDetail::whereMonth('created_at','=',Carbon::today()->month)->sum('payment_amount');
?>
<div class="panel bg-green-400">
    <div class="panel-body padding-15-20">
        <div class="clearfix">
            <div class="pull-left">
                <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0"
                     data-to="{{ $sum }}"
                     data-speed="500" data-refresh-interval="10"></div>
            </div>
            <div class="pull-right">
                <i class="font-size-24 color-green-100 fa fa-inr"></i>
            </div>

            <div class="clearfix"></div>

            <div class="pull-left">
                <div class="display-block color-green-50 font-weight-600">Monthly Collection</div>
            </div>
        </div>
    </div>
</div><!-- /.panel -->
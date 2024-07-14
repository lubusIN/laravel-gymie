<?php
$sum = App\Invoice::sum('pending_amount');
?>

<div class="panel bg-red-400">
    <div class="panel-body padding-15-20">
        <div class="clearfix">
            <div class="pull-left">
                <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0"
                     data-to="{{ $sum }}" data-speed="500" data-refresh-interval="10"></div>
            </div>
            <div class="pull-right">
                <i class="font-size-24 color-red-100 fa fa-money"></i>
            </div>

            <div class="clearfix"></div>

            <div class="pull-left">
                <div class="display-block color-red-50 font-weight-600">Pending Payments</div>
            </div>
        </div>
    </div>
</div><!-- /.panel -->

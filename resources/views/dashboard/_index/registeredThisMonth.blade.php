<?php
use Carbon\Carbon;
$count = App\Member::whereMonth('created_at','=',Carbon::today()->month)->count();
?>

<div class="panel bg-teal-400">
    <div class="panel-body padding-15-20">
        <div class="clearfix">
            <div class="pull-left">
                <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0"
                     data-to="{{ $count }}" data-speed="500"
                     data-refresh-interval="10"></div>
            </div>
            <div class="pull-right">
                <i class="font-size-24 color-teal-100 fa fa-signal"></i>
            </div>

            <div class="clearfix"></div>

            <div class="pull-left">
                <div class="display-block color-teal-50 font-weight-600">Monthly Joinings</div>
            </div>
        </div>
    </div>
</div><!-- /.panel -->

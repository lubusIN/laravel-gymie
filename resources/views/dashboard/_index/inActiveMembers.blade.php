<?php
$count = App\Member::where('status',0)->count();
?>
<div class="panel bg-amber-300">
    <div class="panel-body padding-15-20">
        <div class="clearfix">
            <div class="pull-left">
                <div class="color-white font-size-24 font-roboto font-weight-600" data-toggle="counter" data-start="0" data-from="0"
                     data-to="{{ $count }}" data-speed="500" data-refresh-interval="10"></div>
            </div>
            <div class="pull-right">
                <i class="font-size-24 color-amber-100 fa fa-exclamation-circle"></i>
            </div>

            <div class="clearfix"></div>

            <div class="pull-left">
                <div class="display-block color-amber-50 font-weight-600">Inactive Members</div>
            </div>
        </div>
    </div>
</div><!-- /.panel -->
                
<?php
use Carbon\Carbon;
?>
<div class="row" id="optionBox">
    <div class="col-md-12">
        <div class="panel no-border">
            <div class="panel-title">
                <div class="panel-head font-size-20">Enter details of the subscription</div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-5">
                        {!! Form::label('plan_0','Plan') !!}
                    </div>

                    <div class="col-sm-3">
                        {!! Form::label('start_date_0','Start Date') !!}
                    </div>

                    <div class="col-sm-3">
                        {!! Form::label('end_date_0','End Date') !!}
                    </div>

                    <div class="col-sm-1">
                        <label>&nbsp;</label><br/>
                    </div>
                </div> <!-- / Row -->
                <div id="servicesContainer">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group plan-id">
                                <?php $plans = App\Plan::where('status', '=', '1')->get(); ?>

                                <select id="plan_0" name="plan[0][id]" class="form-control selectpicker show-tick show-menu-arrow childPlan"
                                        data-live-search="true" data-row-id="0">
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" data-price="{{ $plan->amount }}" data-days="{{ $plan->days }}"
                                                data-row-id="0">{{ $plan->plan_display }} </option>
                                    @endforeach
                                </select>
                                <div class="plan-price">
                                    {!! Form::hidden('plan[0][price]','', array('id' => 'price_0')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group plan-start-date">
                                {!! Form::text('plan[0][start_date]',Carbon::today()->format('Y-m-d'),['class'=>'form-control datepicker-startdate childStartDate', 'id' => 'start_date_0', 'data-row-id' => '0']) !!}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group plan-end-date">
                                {!! Form::text('plan[0][end_date]',null,['class'=>'form-control childEndDate', 'id' => 'end_date_0', 'readonly' => 'readonly','data-row-id' => '0']) !!}
                            </div>
                        </div>

                        <div class="col-sm-1">
                            <div class="form-group">
							<span class="btn btn-sm btn-danger pull-right hide remove-service">
							  <i class="fa fa-times"></i>
							</span>
                            </div>
                        </div>
                    </div> <!-- / Row -->
                </div>
                <div class="row">
                    <div class="col-sm-2 pull-right">
                        <div class="form-group">
                            <span class="btn btn-sm btn-primary pull-right" id="addSubscription">Add</span>
                        </div>
                    </div>
                </div>
            </div> <!-- / Panel Body -->

        </div> <!-- /Panel-no-border -->
    </div> <!-- /Main Column -->
</div> <!-- /Main Row -->
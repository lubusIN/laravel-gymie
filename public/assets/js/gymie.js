var gymie = (function ($) {
	"use strict";
	return {
		/* --------------------------------- */
		/* TokenInput
		/* --------------------------------- */
		loadBsTokenInput: function () {
			$('.tokenfield').tokenfield();
		},

		/* --------------------------------- */
		/* Custom send message
		/* --------------------------------- */
		customsendmessage: function () {
			$("#customcontactsdiv").hide();
			$('#custom').on('change', function () {
				if (this.checked) {
					$("#customcontactsdiv").show();
				}
				else {
					$("#customcontactsdiv").hide();
				}
			});
		},

		/* --------------------------------- */
		/* Cheque Details
		/* --------------------------------- */
		chequedetails: function () {

			$("#chequeDetails").hide();

			$('#mode').on('change', function () {
				if (this.value == '0') {
					$("#chequeDetails").show();
				}
				else {
					$("#chequeDetails").hide();
				}

			});

		},

		/* --------------------------------- */
		/* Progress Animation
		/* --------------------------------- */
		loadprogress: function () {
			setTimeout(function () {
				$('.progress-animation .progress-bar').each(function () {
					var me = $(this);
					var perc = me.attr("aria-valuenow");
					var current_perc = 0;
					var progress = setInterval(function () {
						if (current_perc >= perc) {
							clearInterval(progress);
						} else {
							current_perc += 1;
							me.css('width', (current_perc) + '%');
						}
					}, 0);
				});
			}, 0);
		},

		/* --------------------------------- */
		/* Bootstrap Select
		/* --------------------------------- */
		loadbsselect: function () {
			$('select').removeClass('show-menu-arrow');
			$('.selectpicker,select').selectpicker();
		},

		/* --------------------------------- */
		/* Date Picker  : http://eternicode.github.io/bootstrap-datepicker
		/* --------------------------------- */
		loaddatepicker: function () {
			// Default
			$(".datepicker-default").datepicker({
				format: "yyyy-mm-dd",
				autoclose: true,
				todayHighlight: true,
			});
		},

		loaddatepickerstart: function () {
			// Subscriptions Start Date
			/*$(".datepicker-startdate").datepicker({
					format:"yyyy-mm-dd",
					autoclose: true,
					todayHighlight: true,
					startDate: gymieToday,
				});*/


			$(".datepicker-startdate").datepicker({
				format: "yyyy-mm-dd",
				autoclose: true,
				todayHighlight: true,
			});
		},

		loaddatepickerend: function () {
			// Subscriptions End Date
			var subscription_days = $(".plan-data").data('days');
			var subscription_end_date = $("#end_date").val();
			var endDatelimit = moment(subscription_end_date, "YYYY-MM-DD").add(subscription_days, 'days').format("YYYY-MM-DD");


			$(".datepicker-enddate").datepicker({
				format: "yyyy-mm-dd",
				autoclose: true,
				todayHighlight: true,
				startDate: gymieEndDate,
				endDate: gymieDiff,
			});
		},

		/* --------------------------------- */
		/* Charts
		/* --------------------------------- */
		loadmorris: function () {

			// LINE CHART
			var line = new Morris.Line({
				element: 'gymie-registrations-trend',
				resize: true,
				data: JSON.parse(jsRegistraionsCount),
				xkey: 'month',
				ykeys: ['registrations'],
				labels: ['Members'],
				hideHover: 'auto',
				lineColors: ['#27ae60']
			});

			//DONUT CHART
			var donut = new Morris.Donut({
				element: 'gymie-members-per-plan',
				resize: true,
				colors: ["#e74c3c", "#e67e22", "#3498db"],
				data: JSON.parse(jsMembersPerPlan),
				hideHover: 'auto'
			});

		},

		/* --------------------------------- */
		/* iCheck
		/* --------------------------------- */
		loadicheck: function () {
			$('.skin-square input').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green',
				increaseArea: '20%'
			});
		},

		/* --------------------------------- */
		/* Bootstrap File Input
		/* --------------------------------- */
		loadfileinput: function () {
			$('.file-inputs').bootstrapFileInput();
		},

		/* --------------------------------- */
		/* Datepicker
		/* --------------------------------- */
		loaddaterangepicker: function () {
			function cb(start, end) {
				$('.gymie-daterangepicker span').html(moment(start).format('YYYY-MM-DD') + ' - ' + moment(end).format('YYYY-MM-DD'));
				$('#drp_start').val(moment(start).format('YYYY-MM-DD'));
				$('#drp_end').val(moment(end).format('YYYY-MM-DD'));
			}
			//cb(moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD'));

			$('.gymie-daterangepicker').daterangepicker({
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				}
			}, cb);
		},

		/* --------------------------------- */
		/* counter
		/* --------------------------------- */
		loadcounter: function () {
			$("[data-toggle='counter']").countTo();
		},


		/* --------------------------------- */
		/* Enquiries 'Mark as' sweetelert
		/* --------------------------------- */

		markEnquiryAs: function () {
			$('.mark-enquiry-as').click(function () {
				var recordId = $(this).attr("data-record-id");
				var gotoUrl = $(this).attr("data-goto-url");

				markAs(recordId, gotoUrl);
			});

			function markAs(recordId, gotoUrl) {
				swal({
					title: "Are you sure?",
					type: "warning",
					showCancelButton: true,
					showLoaderOnConfirm: true,
					closeOnConfirm: false,
					confirmButtonText: "Yes!",
					confirmButtonColor: "#ec6c62"
				}, function () {
					$.ajax({
						url: gotoUrl,
						type: "POST"
					})
						.done(function (data) {
							swal({
								title: "Done",
								type: "success"
							}, function () {
								location.reload();
							});
						})
						.error(function (data) {
							swal("Oops", "We couldn't connect to the server!", "error");
						});
				});
			}
		},

		/* --------------------------------- */
		/* Member delete sweetalert
		/* --------------------------------- */
		deleterecord: function () {
			$('.delete-record').click(function () {
				var recordId = $(this).attr("data-record-id");
				var deleteUrl = $(this).attr("data-delete-url");

				if ($(this).attr("data-dependency") === 'true') {
					var dependency = $(this).attr("data-dependency");
					var dependencyMessage = $(this).attr("data-dependency-message");
				}
				else {
					var dependency = false;
					var dependencyMessage = "Data dependency";
				}

				recordDelete(recordId, deleteUrl, dependency, dependencyMessage);
			});

			function recordDelete(recordId, deleteUrl, dependency, dependencyMessage) {
				if (dependency) {
					swal("Warning!", dependencyMessage, "warning");
				}
				else {
					swal({
						title: "Are you sure?",
						text: "Deleting this will also delete all its related records , do you still want to delete this record?",
						type: "warning",
						showCancelButton: true,
						showLoaderOnConfirm: true,
						closeOnConfirm: false,
						confirmButtonText: "Yes, delete it!",
						confirmButtonColor: "#ec6c62"
					}, function () {
						$.ajax({
							url: deleteUrl,
							type: "POST"
						})
							.done(function (data) {
								swal({
									title: "Deleted",
									text: "Record has been successfully deleted",
									type: "success"
								}, function () {
									location.reload();
								});
							})
							.error(function (data) {
								swal("Oops", "We couldn't connect to the server!", "error");
							});
					});
				};

			}
		},

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////// SUBSCRIPTION ///////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////

		applyDiscount: function () {
			function getDiscountAmount() {
				var subscription_amount = parseInt($("#subscription_amount").val()); // Get Selected plan amount
				var additional_fees = $("#additional_fees").length ? parseInt($("#additional_fees").val()) : 0; // Get selected admission amount
				var total_amount = subscription_amount + additional_fees;
				var tax = Math.round(total_amount * taxes / 100);  // Calculate taxes
				var total = total_amount + tax;

				var discount_value = parseInt($("#discount_percent").val());
				var discount_amount = isNaN(discount_value) ? $("#discount_amount").val() : Math.round(total * discount_value / 100);
				$("#discount_amount").val(discount_amount);

				var payment_amount = total - discount_amount;
				$("#payment_amount").val(Math.round(payment_amount));
			}

			function getCustomDiscountAmount() {
				var subscription_amount = parseInt($("#subscription_amount").val()); // Get Selected plan amount
				var additional_fees = $("#additional_fees").length ? parseInt($("#additional_fees").val()) : 0; // Get selected admission amount
				var total_amount = subscription_amount + additional_fees;
				var tax = Math.round(total_amount * taxes / 100);  // Calculate taxes
				var total = total_amount + tax;

				var discount_value = $("#discount_amount").val();
				var discount_amount = isNaN(discount_value) ? "" : discount_value;
				var payment_amount = total - discount_amount;
				$("#payment_amount").val(Math.round(payment_amount));
			}

			$("#discount_percent").bind('change keyup input', function (e) {
				getDiscountAmount();
				if ($("#discount_percent").val() != "custom") {
					$("#discount_amount").attr('readonly', true);
				}
				else {
					$("#discount_amount").attr('readonly', false);
				};
			});

			// On amount Change
			$("#discount_amount").bind('change keyup input', function () {
				if ($("#discount_percent").val() == "custom") {
					getCustomDiscountAmount();
				}
			});
		},

		subscription: function () {
			function getEndDate(rowId) {
				var plan_days = $('.plan-id select#plan_' + rowId + ' option:selected').data('days') - 1;
				var subscription_start_date = $('.plan-start-date input#start_date_' + rowId).val();
				var subscription_end_date = moment(subscription_start_date, "YYYY-MM-DD").add(plan_days, 'days').format("YYYY-MM-DD");

				$('.plan-end-date input#end_date_' + rowId).val(subscription_end_date);
			}

			function getPlanAmount() {
				var sum = 0;
				$(".plan-id select option:selected").each(function () {
					sum += +$(this).data('price');
					$('.plan-price input#price_' + $(this).data('row-id')).val($(this).data('price'));
				});
				$("#subscription_amount").val(sum).trigger('change');
			}

			function getTaxAmount() {
				var subscription_amount = parseInt($("#subscription_amount").val()); // Get Selected plan amount
				var additional_fees = $("#additional_fees").length ? parseInt($("#additional_fees").val()) : 0; // Get selected admission amount
				var total_amount = subscription_amount + additional_fees;
				var tax = Math.round(total_amount * taxes / 100);  // Calculate taxes

				$("#taxes_amount").val(tax);
			}

			function getDiscountAmount() {
				var subscription_amount = parseInt($("#subscription_amount").val()); // Get Selected plan amount
				var additional_fees = $("#additional_fees").length ? parseInt($("#additional_fees").val()) : 0; // Get selected admission amount
				var total_amount = subscription_amount + additional_fees;
				var tax = Math.round(total_amount * taxes / 100);  // Calculate taxes
				var total = total_amount + tax; // Calculate total and prefill in payment_amount box

				var discount_value = parseInt($("#discount_percent").val());
				var discount_amount = isNaN(discount_value) ? $("#discount_amount").val() : Math.round(total * discount_value / 100);
				$("#discount_amount").val(discount_amount);

				var payment_amount = total - discount_amount; // Calculate total and prefill in payment_amount box

				$("#payment_amount").val(Math.round(payment_amount));
				$('#payment_amount').data('amounttotal', $('#payment_amount').val());
			}

			function getCustomDiscountAmount() {
				var subscription_amount = parseInt($("#subscription_amount").val()); // Get Selected plan amount
				var additional_fees = $("#additional_fees").length ? parseInt($("#additional_fees").val()) : 0; // Get selected admission amount
				var total_amount = subscription_amount + additional_fees;
				var tax = Math.round(total_amount * taxes / 100);  // Calculate taxes
				var total = total_amount + tax; // Calculate total and prefill in payment_amount box

				var discount_value = $("#discount_amount").val();
				var discount_amount = isNaN(discount_value) ? "" : discount_value;
				var payment_amount = total - discount_amount; // Calculate total and prefill in payment_amount box

				$("#payment_amount").val(Math.round(payment_amount));
				$('#payment_amount').data('amounttotal', $('#payment_amount').val());
			}

			$(document).ready(function () {
				getEndDate(0);
				getPlanAmount();
				$("#payment_amount_pending").val(0);
			});

			//On dropdown change set end date and plan amount
			$(document).on('change', '.plan-id select', function () {
				getEndDate($(this).data('row-id'));
				getPlanAmount();
				$(this).selectpicker('refresh');
			});

			// On start date Change update end datepicker
			$(document).on('change keyup input', '.plan-start-date input', function () {
				getEndDate($(this).data('row-id'));
			});

			// On subscription amount Change tax & discount amount
			$("#subscription_amount").bind('change keyup input', function (e) {
				getTaxAmount();
				getDiscountAmount();
			});

			// On Admission Change
			$("#additional_fees").bind('change keyup input', function () {
				getTaxAmount();
				getDiscountAmount();
			});

			//OnDiscount Percent dropdown change
			$("#discount_percent").bind('change keyup input', function (e) {
				getDiscountAmount();
				if ($("#discount_percent").val() != "custom") {
					$("#discount_amount").attr('readonly', true);
				}
				else {
					$("#discount_amount").attr('readonly', false);
				};
			});

			// On amount Change
			$("#discount_amount").bind('change keyup input', function () {
				if ($("#discount_percent").val() == "custom") {
					getCustomDiscountAmount();
				}
			});

			// On payment received amount Change
			$("#payment_amount").bind('change keyup input', function () {
				var payment_total = $('#payment_amount').data('amounttotal');
				if ($('#previous_payment').length) {
					var pending = payment_total - parseInt($("#previous_payment").val()) - parseInt($("#payment_amount").val());
				}
				else {
					var pending = payment_total - parseInt($("#payment_amount").val());
				}
				$("#payment_amount_pending").val((isNaN(pending) ? 0 : pending));
			});

			// Multiple Services
			var x = (typeof currentServices != 'undefined' ? currentServices - 1 : 0); //initlal text box count
			var max_fields = servicesCount - 1; //maximum input boxes allowed
			if (x == max_fields) {
				$("#addSubscription").hide();
			}

			$('#addSubscription').click(function () {
				if (x < max_fields) {
					x++; // Increment counter to add new fields

					// Clone base field set and clean it
					$("#servicesContainer>.row:first-child").clone().appendTo("#servicesContainer"); // Add new plan subscription
					$("#servicesContainer>.row:last-child .remove-service").removeClass("hide"); // Active remove button for additional services
					$("#servicesContainer>.row:last-child .bootstrap-select").remove(); // remove bootstrap select

					// Remove cloned validation
					$("#servicesContainer>.row:last-child .plan-start-date>small").remove(); // remove bootstrap validator
					$("#servicesContainer>.row:last-child .plan-start-date>input").removeAttr('data-bv-field'); // remove bootstrap validator data attr from field

					// Set Unique Ids and names
					$("#servicesContainer>.row:last-child .plan-id>select").attr("id", "plan_" + x).attr("name", "plan[" + x + "][id]").attr('data-row-id', x);
					$("#servicesContainer>.row:last-child .plan-id>select>option").attr('data-row-id', x);
					$("#servicesContainer>.row:last-child .plan-start-date>input").attr("id", "start_date_" + x).attr("name", "plan[" + x + "][start_date]").attr('data-row-id', x);
					$("#servicesContainer>.row:last-child .plan-end-date>input").attr("id", "end_date_" + x).attr("name", "plan[" + x + "][end_date]").attr('data-row-id', x);
					$("#servicesContainer>.row:last-child .plan-price>input").attr("id", "price_" + x).attr("name", "plan[" + x + "][price]").attr('data-row-id', x);

					// Adding Validators
					// $('#membersform').bootstrapValidator('addField', 'plan[' + x + '].start_date', startDateValidators);

					// Reattach 3rd Party plugins
					gymie.loadbsselect();
					gymie.loaddatepickerstart();

					// Rerun needed logic
					getPlanAmount();

					//Disable ADD btn
					if (x == max_fields) {
						$("#addSubscription").hide();
					}
				}
				else {
					// Else logic
				}
			});

			$('#servicesContainer').on('click', 'span.remove-service', function () {
				$(this).closest(".row").remove();
				x--; // Reduce max counter
				getPlanAmount();
				//Enable ADD btn
				if (x < max_fields) {
					$("#addSubscription").show();
				}
			});
		},

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////

		subscriptionChange: function () {
			function getAmountToPay() {
				var totalAmount = parseInt($("#payment_amount").val());
				var alreadyPaid = parseInt($("#previous_payment").val());
				var newTotal = totalAmount - alreadyPaid;
				$("#payment_amount").val(newTotal);
			}

			$(document).ready(function () {
				getAmountToPay();
			});

			// On plans dropdown change
			$(document).on('change', '.plan-id select', function () {
				getAmountToPay();
			});

			// On discount dropdown change
			$("#discount_amount").bind('change keyup input', function () {
				getAmountToPay();
			});
		},
	};
})(jQuery);

$(document).ready(function() {
				$('#paymentsform').bootstrapValidator({
					fields: {
						payment_amount: {
							validators: {
								notEmpty: {
									message: 'The amount is required and can\'t be empty'
								},
							}
						},
						invoice_id: {
							  validators: {
								  notEmpty: {
									message: 'The invoice number is required and can\'t be empty'
								}
							}
						},
						date: {
							  validators: {
								  notEmpty: {
									message: 'The cheque date is required and can\'t be empty'
								}
							}
						},
						number: {
							  validators: {
								  notEmpty: {
									message: 'The cheque number is required and can\'t be empty'
								}
							}
						},
					}
				});
			});
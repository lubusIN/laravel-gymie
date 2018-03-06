$(document).ready(function() {
				$('#subscriptionschangeform').bootstrapValidator({
					fields: {
						end_date: {
							validators: {
								notEmpty: {
									message: 'The end date is required and can\'t be empty'
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
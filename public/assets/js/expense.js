$(document).ready(function() {
				$('#expensesform').bootstrapValidator({
					fields: {
						name: {
							validators: {
								notEmpty: {
									message: 'The name is required and can\'t be empty'
								},
								stringLength: {
			                        max: 50,
			                        message: 'It must be less than 50 characters'
			                    }
							}
						},
						category_id: {
							validators: {
								notEmpty: {
									message: 'The category is required and can\'t be empty'
								}
							}
						},
						amount: {
							validators: {
								notEmpty: {
									message: 'Amount cannot be empty'
								},
								regexp: {
									regexp: /^[0-9\.]+$/,
									message: 'The amount can only consist of numbers and dot'
								}
							

							}
						}
					
				}
			});
			});
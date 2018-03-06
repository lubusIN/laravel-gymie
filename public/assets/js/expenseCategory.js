$(document).ready(function() {
				$('#expensecategoriesform').bootstrapValidator({
					fields: {
						name: {
							validators: {
								notEmpty: {
									message: 'The category name is required and can\'t be empty'
								},
								stringLength: {
			                        max: 50,
			                        message: 'It must be less than 50 characters'
			                    }
							}
						}
					}
				});
			});
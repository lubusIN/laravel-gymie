$(document).ready(function() {
				$('#sendform').bootstrapValidator({
					fields: {
						message: {
							validators: {
								notEmpty: {
									message: 'The message text is required and can\'t be empty'
								},
								stringLength: {
			                        max: 420,
			                        message: 'It must be less than 420 characters'
			                    }
							}
						}
					}
				});
			});
$(document).ready(function() {
				$('#smseventsform').bootstrapValidator({
					fields: {
						date: {
							validators: {
								notEmpty: {
									message: 'The event date is required and can\'t be empty'
								}
							}
						},
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
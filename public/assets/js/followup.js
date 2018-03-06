$(document).ready(function() {
				$('#followupform').bootstrapValidator({
					fields: {
						outcome: {
							validators: {
								notEmpty: {
									message: 'The outcome is required and can\'t be empty'
								}
							}
						}
					}
				})
			});
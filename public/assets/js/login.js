$(document).ready(function() {
				$('#loginform').bootstrapValidator({
					fields: {
						email: {
							validators: {
								notEmpty: {
									message: 'The email address is required and can\'t be empty'
								},
								emailAddress: {
									message: 'The input is not a valid email address'
								}
							}
						},
						 password: {
            				validators: {
            					notEmpty: {
									message: 'The password is required and can\'t be empty'
								},
           					 }
       					 },
					}
			});
			});
$(document).ready(function() {
				$('#usersform').bootstrapValidator({
					fields: {
						name: {
							validators: {
								notEmpty: {
									message: 'The name is required and can\'t be empty'
								}
							}
						},
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
								stringLength: {
			                        min: 6,
			                        message: 'The password must be atleast of 6 characters'
			                    },
                				identical: {
                   				 field: 'password_confirmation',
                   				 message: 'The password and its confirm are not the same'
               					 }
           					 }
       					 },
						password_confirmation: {
           					 validators: {
           					 	notEmpty: {
									message: 'Password confirmation is required and can\'t be empty'
								},
               					 identical: {
                   					field: 'password',
                   					message: 'The password and its confirm are not the same'
               				    }
            				}
        				}
					}
			});
			});
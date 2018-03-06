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
                        }
                    }
            });
            });
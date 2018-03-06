$(document).ready(function() {
				$('#enquiriesform').bootstrapValidator({
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
						address: {
							validators: {
								notEmpty: {
									message: 'The address is required and can\'t be empty'
								},
								stringLength: {
			                        max: 200,
			                        message: 'It must be less than 200 characters'
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
								},
								stringLength: {
			                        max: 50,
			                        message: 'It must be less than 50 characters'
			                    }
							}
						},
						gender: {
							validators: {
								notEmpty: {
									message: 'The gender is required and can\'t be empty'
								}
							}
						},
						pin_code: {
							validators: {
								notEmpty: {
									message: 'The Pin Code is required and can\'t be empty'
								},
								regexp: {
									regexp: /^[0-9_\.]+$/,
									message: 'The input is not a valid pin code'
								}
							}
						},
						occupation: {
							validators: {
								notEmpty: {
									message: 'The occupation is required and can\'t be empty'
								},
								stringLength: {
			                        max: 50,
			                        message: 'It must be less than 50 characters'
			                    }
							}
						},
						aim: {
							validators: {
								notEmpty: {
									message: 'The aim is required and can\'t be empty'
								},
								stringLength: {
			                        max: 50,
			                        message: 'It must be less than 50 characters'
			                    }
							}
						},
						source: {
							validators: {
								notEmpty: {
									message: 'The source is required and can\'t be empty'
								},
								stringLength: {
			                        max: 50,
			                        message: 'It must be less than 50 characters'
			                    }
							}
						},
						date: {
							validators: {
								notEmpty: {
									message: 'The date is required and can\'t be empty'
								}
							}
						},
						due_date: {
							validators: {
								notEmpty: {
									message: 'The due date is required and can\'t be empty'
								}
							}
						},
						followup_by: {
							validators: {
								notEmpty: {
									message: 'The field is required and can\'t be empty'
								}
							}
						},
						status: {
							validators: {
								notEmpty: {
									message: 'The status is required and can\'t be empty'
								}
							}
						},
						outcome: {
							validators: {
								notEmpty: {
									message: 'The outcome is required and can\'t be empty'
								}
							}
						},
						interested_in: {
							validators: {
								notEmpty: {
									message: 'The field is required and can\'t be empty'
								},
								stringLength: {
			                        max: 50,
			                        message: 'It must be less than 50 characters'
			                    }
							}
						},
						contact: {
							validators: {
								notEmpty: {
									message: 'The contact is required and can\'t be empty'
								},
								regexp: {
									regexp: /^[0-9_\.]+$/,
									message: 'The input is not a valid number'
								},
								stringLength: {
			                        max: 10,
			                        message: 'It must be less than 11 characters'
			                    }
							}
						}
					}
				});
			});


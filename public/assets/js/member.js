var startDateValidators = {
			            row: '.plan-start-date',
			            validators: {
			                notEmpty: {
			                    message: 'The start date is required'
			                }
			            }
			        };

	$('#membersform').bootstrapValidator({
		fields: {
			member_code: {
				validators: {
					notEmpty: {
						message: 'The member code is required and can\'t be empty'
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
			DOB: {
				validators: {
					notEmpty: {
						message: 'The date of birth is required'
					},
					date: {
                        format: 'YYYY-MM-DD',
                        message: 'The date should be in YYYY-MM-DD format'
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
			health_issues: {
				validators: {
					notEmpty: {
						message: 'This field required and can\'t be empty'
					}
				}
			},
			proof_name: {
				validators: {
					notEmpty: {
						message: 'The proof name is required and can\'t be empty'
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
			plan_id: {
				validators: {
					notEmpty: {
						message: 'The plan id is required and can\'t be empty'
					}
				}
			},
			pin_code: {
				validators: {
					notEmpty: {
						message: 'The Pin Code is required and can\'t be empty'
					},
					regexp: {
						regexp: /^[0-9\.]+$/,
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
			invoice_number: {
				validators: {
					notEmpty: {
						message: 'The invoice number is required and can\'t be empty'
					}
				}
			},
			admission_amount: {
				validators: {
					notEmpty: {
						message: 'The admission amount is required and can\'t be empty'
					},
					regexp: {
						regexp: /^[0-9\.]+$/,
						message: 'The input is not a valid amount'
					}
				}
			},
			subscription_amount: {
				validators: {
					notEmpty: {
						message: 'The subscription amount is required and can\'t be empty'
					},
					regexp: {
						regexp: /^[0-9\.]+$/,
						message: 'The input is not a valid amount'
					}
				}
			},
			taxes_amount: {
				validators: {
					notEmpty: {
						message: 'The taxes amount is required and can\'t be empty'
					},
					regexp: {
						regexp: /^[0-9\.]+$/,
						message: 'The input is not a valid amount'
					}
				}
			},
			payment_amount: {
				validators: {
					notEmpty: {
						message: 'The amount is required and can\'t be empty'
					},
					regexp: {
						regexp: /^[0-9\.]+$/,
						message: 'The input is not a valid amount'
					}
				}
			},
			invoice_id: {
				  validators: {
					  notEmpty: {
						message: 'The invoice number is required and can\'t be empty'
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
			contact: {
				validators: {
					notEmpty: {
						message: 'The contact is required and can\'t be empty'
					},
					regexp: {
						regexp: /^[0-9\.]+$/,
						message: 'The input is not a valid number'
					},
					stringLength: {
                        max: 10,
                        message: 'It must be less than 10 characters'
                    }
				}
			},
			emergency_contact: {
				validators: {
					notEmpty: {
						message: 'The contact is required and can\'t be empty'
					},
					regexp: {
						regexp: /^[0-9\.]+$/,
						message: 'The input is not a valid number'
					},
					stringLength: {
                        max: 10,
                        message: 'It must be less than 10 characters'
                    }
				}
			},
			'plan[0].start_date' : startDateValidators								          
		}
	});
	


<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


// Log viewer route
Route::get('logs',['middleware' => ['auth','role:Gymie'],'uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index']);

//Data Migration
Route::get('data/migration',['middleware' => ['auth','role:Gymie'],'uses' => 'DataMigrationController@migrate']);
Route::get('data/media/migration',['middleware' => ['auth','role:Gymie'],'uses' => 'DataMigrationController@migrateMedia']);
Route::get('excel/migration',['middleware' => ['auth','role:Gymie'],'uses' => 'DataMigrationController@migrateExcel']);

//Report DATA
Route::get('reportData/members','ReportData\MembersController@details');

//API routes
Route::post('api/token','Api\AuthenticateController@authenticate');

Route::group(['prefix' => 'api','middleware' => ['jwt.auth']], function() {	
	Route::get('dashboard','Api\DashboardController@index');
	Route::get('members','Api\MembersController@index');
	Route::get('subscriptions','Api\SubscriptionsController@index');
	Route::get('payments','Api\PaymentsController@index');
	Route::get('invoices','Api\InvoicesController@index');
	Route::get('invoices/paid','Api\InvoicesController@paid');
	Route::get('invoices/unpaid','Api\InvoicesController@unpaid');
	Route::get('invoices/partial','Api\InvoicesController@partial');
	Route::get('invoices/overpaid','Api\InvoicesController@overpaid');
	Route::get('enquiries','Api\EnquiriesController@index');
	Route::get('settings','Api\SettingsController@index');
	Route::get('plans','Api\PlansController@index');
	Route::get('expenseCategories','Api\ExpenseCategoriesController@index');
	Route::get('expenses','Api\ExpensesController@index');
	Route::get('subscriptions/expiring','Api\SubscriptionsController@expiring');
	Route::get('subscriptions/expired','Api\SubscriptionsController@expired');
	Route::get('members/{id}','Api\MembersController@show');
	Route::get('subscriptions/{id}','Api\SubscriptionsController@show');
	Route::get('payments/{id}','Api\PaymentsController@show');
	Route::get('invoices/{id}','Api\InvoicesController@show');
	Route::get('enquiries/{id}','Api\EnquiriesController@show');
	Route::get('plans/{id}','Api\PlansController@show');
	Route::get('expenseCategories/{id}','Api\ExpenseCategoriesController@show');
	Route::get('expenses/{id}','Api\ExpensesController@show');
});

//Auth routes
Route::group(['prefix' => 'auth'], function() {
	Route::get('login', 'Auth\AuthController@getLogin');
	Route::post('login', 'Auth\AuthController@postLogin');
	Route::get('logout', 'Auth\AuthController@getLogout');
});

//dashboard 
Route::group(['middleware' => ['auth']], function() {
Route::get('/','DashboardController@index');
Route::get('/dashboard','DashboardController@index');
Route::post('/dashboard/smsRequest','DashboardController@smsRequest');
});

//MembersController
Route::group(['prefix' => 'members','middleware' => ['auth']], function() {
	Route::get('/',['middleware' => ['permission:manage-gymie|manage-members|view-member'],'uses' => 'MembersController@index']);
	Route::get('all',['middleware' => ['permission:manage-gymie|manage-members|view-member'],'uses' => 'MembersController@index']);
	Route::get('active',['middleware' => ['permission:manage-gymie|manage-members|view-member'],'uses' => 'MembersController@active']);
	Route::get('inactive',['middleware' => ['permission:manage-gymie|manage-members|view-member'],'uses' => 'MembersController@inactive']);
	Route::get('create',['middleware' => ['permission:manage-gymie|manage-members|add-member'],'uses' => 'MembersController@create']);
	Route::post('/',['middleware' => ['permission:manage-gymie|manage-members|add-member'],'uses' => 'MembersController@store']);
	Route::get('{id}/show',['middleware' => ['permission:manage-gymie|manage-members|view-member'],'uses' => 'MembersController@show']);
	Route::get('{id}/edit',['middleware' => ['permission:manage-gymie|manage-members|edit-member'],'uses' => 'MembersController@edit']);
	Route::post('{id}/update',['middleware' => ['permission:manage-gymie|manage-members|edit-member'],'uses' => 'MembersController@update']);
	Route::post('{id}/archive',['middleware' => ['permission:manage-gymie|manage-members|delete-member'],'uses' => 'MembersController@archive']);
	Route::get('{id}/transfer',['middleware' => ['permission:manage-gymie|manage-enquiries|transfer-enquiry'],'uses' => 'MembersController@transfer']);
});

//ReportsController
Route::group(['prefix' => 'reports','middleware' => ['auth']], function() {
	Route::get('members/charts',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@gymMemberCharts']);
	Route::get('members/data',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@gymMemberData']);
	Route::get('enquiries/charts',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@enquiryCharts']);
	Route::get('enquiries/data',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@enquiryData']);
	Route::get('subscriptions/charts',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@subscriptionCharts']);
	Route::get('subscriptions/data',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@subscriptionData']);
	Route::get('payments/charts',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@paymentCharts']);
	Route::get('payments/data',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@paymentData']);
	Route::get('expenses/charts',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@expenseCharts']);
	Route::get('expenses/data',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@expenseData']);
	Route::get('invoices/charts',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@invoiceCharts']);
	Route::get('invoices/data',['middleware' => ['permission:manage-gymie|manage-reports|view-report'],'uses' => 'ReportsController@invoiceData']);
});

//SmsController
Route::group(['prefix' => 'sms','middleware' => ['auth']], function() {
	Route::get('triggers',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@triggersIndex']);
	Route::post('triggers/update',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@triggerUpdate']);
	Route::get('events',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@eventsIndex']);
	Route::get('events/create',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@createEvent']);
	Route::post('events',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@storeEvent']);
	Route::get('events/{id}/edit',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@editEvent']);
	Route::post('events/{id}/update',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@updateEvent']);
	Route::post('events/{id}/destroy',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@destroyEvent']);
	Route::get('send',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@send']);
	Route::post('shoot',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@shoot']);
	Route::get('log',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@logIndex']);
	Route::get('log/refresh',['middleware' => ['permission:manage-gymie|manage-sms'],'uses' => 'SmsController@logRefresh']);
});

//enquiries
Route::group(['prefix' => 'enquiries','middleware' => ['auth']], function() {
	Route::get('/',['middleware' => ['permission:manage-gymie|manage-enquiries|view-enquiry'],'uses' => 'EnquiriesController@index']);
	Route::get('all',['middleware' => ['permission:manage-gymie|manage-enquiries|view-enquiry'],'uses' => 'EnquiriesController@index']);
	Route::get('create',['middleware' => ['permission:manage-gymie|manage-enquiries|add-enquiry'],'uses' => 'EnquiriesController@create']);
	Route::post('/',['middleware' => ['permission:manage-gymie|manage-enquiries|add-enquiry'],'uses' => 'EnquiriesController@store']);
	Route::get('{id}/show',['middleware' => ['permission:manage-gymie|manage-enquiries|view-enquiry'],'uses' => 'EnquiriesController@show']);
	Route::post('{id}/lost',['middleware' => ['permission:manage-gymie|manage-enquiries|view-enquiry'],'uses' => 'EnquiriesController@lost']);
	Route::post('{id}/markMember',['middleware' => ['permission:manage-gymie|manage-enquiries|view-enquiry'],'uses' => 'EnquiriesController@markMember']);
	Route::get('{id}/edit',['middleware' => ['permission:manage-gymie|manage-enquiries|edit-enquiry'],'uses' => 'EnquiriesController@edit']);
	Route::post('{id}/update',['middleware' => ['permission:manage-gymie|manage-enquiries|edit-enquiry'],'uses' => 'EnquiriesController@update']);
});

//followups
Route::group(['prefix' => 'enquiry','middleware' => ['auth']], function() {
	Route::post('followups',['middleware' => ['permission:manage-gymie|manage-enquiries|add-enquiry-followup'],'uses' => 'FollowupsController@store']);
	Route::post('followups/{id}/update',['middleware' => ['permission:manage-gymie|manage-enquiries|edit-enquiry-followup'],'uses' => 'FollowupsController@update']);
});

//plans
Route::group(['prefix' => 'plans','middleware' => ['auth']], function() {
	Route::get('/',['middleware' => ['permission:manage-gymie|manage-plans|view-plan'],'uses' => 'PlansController@index']);
	Route::get('all',['middleware' => ['permission:manage-gymie|manage-plans|view-plan'],'uses' => 'PlansController@index']);
	Route::get('show',['middleware' => ['permission:manage-gymie|manage-plans|view-plan'],'uses' => 'PlansController@show']);
	Route::get('create',['middleware' => ['permission:manage-gymie|manage-plans|add-plan'],'uses' => 'PlansController@create']);
	Route::post('/',['middleware' => ['permission:manage-gymie|manage-plans|add-plan'],'uses' => 'PlansController@store']);
	Route::get('{id}/edit',['middleware' => ['permission:manage-gymie|manage-plans|edit-plan'],'uses' => 'PlansController@edit']);
	Route::post('{id}/update',['middleware' => ['permission:manage-gymie|manage-plans|edit-plan'],'uses' => 'PlansController@update']);
	Route::post('{id}/archive',['middleware' => ['permission:manage-gymie|manage-plans|delete-plan'],'uses' => 'PlansController@archive']);
	Route::get('/services',['middleware' => ['permission:manage-gymie|manage-services|view-service'],'uses' => 'ServicesController@index']);
	Route::get('services/all',['middleware' => ['permission:manage-gymie|manage-services|view-service'],'uses' => 'ServicesController@index']);
	Route::get('services/create',['middleware' => ['permission:manage-gymie|manage-services|add-service'],'uses' => 'ServicesController@create']);
	Route::post('/services',['middleware' => ['permission:manage-gymie|manage-services|add-service'],'uses' => 'ServicesController@store']);
	Route::get('services/{id}/edit',['middleware' => ['permission:manage-gymie|manage-services|edit-service'],'uses' => 'ServicesController@edit']);
	Route::post('services/{id}/update',['middleware' => ['permission:manage-gymie|manage-services|edit-service'],'uses' => 'ServicesController@update']);
	Route::post('services/{id}/delete',['middleware' => ['permission:manage-gymie|manage-services|delete-service'],'uses' => 'ServicesController@delete']);
});

//services
// Route::group(['prefix' => 'services','middleware' => ['auth']], function() {
// 	Route::get('/',['middleware' => ['permission:manage-gymie|manage-services|view-service'],'uses' => 'ServicesController@index']);
// 	Route::get('all',['middleware' => ['permission:manage-gymie|manage-services|view-service'],'uses' => 'ServicesController@index']);
// 	Route::get('create',['middleware' => ['permission:manage-gymie|manage-services|add-service'],'uses' => 'ServicesController@create']);
// 	Route::post('/',['middleware' => ['permission:manage-gymie|manage-services|add-service'],'uses' => 'ServicesController@store']);
// 	Route::get('{id}/edit',['middleware' => ['permission:manage-gymie|manage-services|edit-service'],'uses' => 'ServicesController@edit']);
// 	Route::post('{id}/update',['middleware' => ['permission:manage-gymie|manage-services|edit-service'],'uses' => 'ServicesController@update']);
// });

//subsciptions
Route::group(['prefix' => 'subscriptions','middleware' => ['auth']], function() {
	Route::get('/',['middleware' => ['permission:manage-gymie|manage-subscriptions|view-subscription'],'uses' => 'SubscriptionsController@index']);
	Route::get('all',['middleware' => ['permission:manage-gymie|manage-subscriptions|view-subscription'],'uses' => 'SubscriptionsController@index']);
	Route::get('expiring',['middleware' => ['permission:manage-gymie|manage-subscriptions|view-subscription'],'uses' => 'SubscriptionsController@expiring']);
	Route::get('expired',['middleware' => ['permission:manage-gymie|manage-subscriptions|view-subscription'],'uses' => 'SubscriptionsController@expired']);
	Route::get('create',['middleware' => ['permission:manage-gymie|manage-subscriptions|add-subscription'],'uses' => 'SubscriptionsController@create']);
	Route::post('/',['middleware' => ['permission:manage-gymie|manage-subscriptions|add-subscription'],'uses' => 'SubscriptionsController@store']);
	Route::get('{id}/show',['middleware' => ['permission:manage-gymie|manage-subscriptions|view-subscription'],'uses' => 'SubscriptionsController@show']);
	Route::get('{id}/edit',['middleware' => ['permission:manage-gymie|manage-subscriptions|edit-subscription'],'uses' => 'SubscriptionsController@edit']);
	Route::post('{id}/update',['middleware' => ['permission:manage-gymie|manage-subscriptions|edit-subscription'],'uses' => 'SubscriptionsController@update']);
	Route::get('{id}/change',['middleware' => ['permission:manage-gymie|manage-subscriptions|change-subscription'],'uses' => 'SubscriptionsController@change']);
	Route::post('{id}/modify',['middleware' => ['permission:manage-gymie|manage-subscriptions|change-subscription'],'uses' => 'SubscriptionsController@modify']);
	Route::get('{id}/renew',['middleware' => ['permission:manage-gymie|manage-subscriptions|renew-subscription'],'uses' => 'SubscriptionsController@renew']);
	Route::post('{id}/cancelSubscription',['middleware' => ['permission:manage-gymie|manage-subscriptions|cancel-subscription'],'uses' => 'SubscriptionsController@cancelSubscription']);
	Route::post('{id}/delete',['middleware' => ['permission:manage-gymie|manage-subscriptions|delete-subscription'],'uses' => 'SubscriptionsController@delete']);
});

//invoices
Route::group(['prefix' => 'invoices','middleware' => ['auth']], function() {
	Route::get('/',['middleware' => ['permission:manage-gymie|manage-invoices|view-invoice'],'uses' => 'InvoicesController@index']);
	Route::get('all',['middleware' => ['permission:manage-gymie|manage-invoices|view-invoice'],'uses' => 'InvoicesController@index']);
	Route::get('paid',['middleware' => ['permission:manage-gymie|manage-invoices|view-invoice'],'uses' => 'InvoicesController@paid']);
	Route::get('unpaid',['middleware' => ['permission:manage-gymie|manage-invoices|view-invoice'],'uses' => 'InvoicesController@unpaid']);
	Route::get('partial',['middleware' => ['permission:manage-gymie|manage-invoices|view-invoice'],'uses' => 'InvoicesController@partial']);
	Route::get('overpaid',['middleware' => ['permission:manage-gymie|manage-invoices|view-invoice'],'uses' => 'InvoicesController@overpaid']);
	Route::get('{id}/show',['middleware' => ['permission:manage-gymie|manage-invoices|view-invoice'],'uses' => 'InvoicesController@show']);
	Route::get('{id}/payment',['middleware' => ['permission:manage-gymie|manage-invoices|add-payment'],'uses' => 'InvoicesController@createPayment']);
	Route::post('{id}/delete',['middleware' => ['permission:manage-gymie|manage-invoices|delete-invoice'],'uses' => 'InvoicesController@delete']);
	Route::get('{id}/discount',['middleware' => ['permission:manage-gymie|manage-invoices|add-discount'],'uses' => 'InvoicesController@discount']);
	Route::post('{id}/applyDiscount',['middleware' => ['permission:manage-gymie|manage-invoices|add-discount'],'uses' => 'InvoicesController@applyDiscount']);
});

//activities
// Route::get('access_logs','Access_logsController@index');
// Route::get('access_logs/show','Access_logsController@show');
// Route::get('access_logs/create','Access_logsController@create');

//payments
Route::group(['prefix' => 'payments','middleware' => ['auth']], function() {
	Route::get('/',['middleware' => ['permission:manage-gymie|manage-payments|view-payment'],'uses' => 'PaymentsController@index']);
	Route::get('all',['middleware' => ['permission:manage-gymie|manage-payments|view-payment'],'uses' => 'PaymentsController@index']);
	Route::get('show',['middleware' => ['permission:manage-gymie|manage-payments|view-payment'],'uses' => 'PaymentsController@show']);
	Route::get('create',['middleware' => ['permission:manage-gymie|manage-payments|add-payment'],'uses' => 'PaymentsController@create']);
	Route::post('/',['middleware' => ['permission:manage-gymie|manage-payments|add-payment'],'uses' => 'PaymentsController@store']);	
	Route::get('{id}/edit',['middleware' => ['permission:manage-gymie|manage-payments|edit-payment'],'uses' => 'PaymentsController@edit']);
	Route::get('{id}/clearCheque',['middleware' => ['permission:manage-gymie|manage-payments|edit-payment'],'uses' => 'PaymentsController@clearCheque']);
	Route::get('{id}/depositCheque',['middleware' => ['permission:manage-gymie|manage-payments|edit-payment'],'uses' => 'PaymentsController@depositCheque']);
	Route::get('{id}/chequeBounce',['middleware' => ['permission:manage-gymie|manage-payments|edit-payment'],'uses' => 'PaymentsController@chequeBounce']);
	Route::get('{id}/chequeReissue',['middleware' => ['permission:manage-gymie|manage-payments|edit-payment'],'uses' => 'PaymentsController@chequeReissue']);
	Route::post('{id}/update',['middleware' => ['permission:manage-gymie|manage-payments|edit-payment'],'uses' => 'PaymentsController@update']);
	Route::post('{id}/delete',['middleware' => ['permission:manage-gymie|manage-payments|delete-payment'],'uses' => 'PaymentsController@delete']);
});

//expense Categories
// Route::group(['prefix' => 'expenseCategories','middleware' => ['auth']], function() {
// 	Route::get('/',['middleware' => ['permission:manage-gymie|manage-expenseCategories|view-expenseCategory'],'uses' => 'ExpenseCategoriesController@index']);
// 	Route::get('all',['middleware' => ['permission:manage-gymie|manage-expenseCategories|view-expenseCategory'],'uses' => 'ExpenseCategoriesController@index']);
// 	Route::get('create',['middleware' => ['permission:manage-gymie|manage-expenseCategories|add-expenseCategory'],'uses' => 'ExpenseCategoriesController@create']);
// 	Route::post('/',['middleware' => ['permission:manage-gymie|manage-expenseCategories|add-expenseCategory'],'uses' => 'ExpenseCategoriesController@store']);
// 	Route::get('{id}/edit',['middleware' => ['permission:manage-gymie|manage-expenseCategories|edit-expenseCategory'],'uses' => 'ExpenseCategoriesController@edit']);
// 	Route::post('{id}/update',['middleware' => ['permission:manage-gymie|manage-expenseCategories|edit-expenseCategory'],'uses' => 'ExpenseCategoriesController@update']);
// 	Route::post('{id}/archive',['middleware' => ['permission:manage-gymie|manage-expenseCategories|delete-expenseCategory'],'uses' => 'ExpenseCategoriesController@archive']);
// });


//expenses
Route::group(['prefix' => 'expenses','middleware' => ['auth']], function() {
	Route::get('/',['middleware' => ['permission:manage-gymie|manage-expenses|view-expense'],'uses' => 'ExpensesController@index']);
	Route::get('all',['middleware' => ['permission:manage-gymie|manage-expenses|view-expense'],'uses' => 'ExpensesController@index']);
	Route::get('show',['middleware' => ['permission:manage-gymie|manage-expenses|view-expense'],'uses' => 'ExpensesController@show']);
	Route::get('create',['middleware' => ['permission:manage-gymie|manage-expenses|add-expense'],'uses' => 'ExpensesController@create']);
	Route::post('/',['middleware' => ['permission:manage-gymie|manage-expenses|add-expense'],'uses' => 'ExpensesController@store']);
	Route::get('{id}/edit',['middleware' => ['permission:manage-gymie|manage-expenses|edit-expense'],'uses' => 'ExpensesController@edit']);
	Route::post('{id}/update',['middleware' => ['permission:manage-gymie|manage-expenses|edit-expense'],'uses' => 'ExpensesController@update']);
	Route::get('{id}/paid',['middleware' => ['permission:manage-gymie|manage-expenses|edit-expense'],'uses' => 'ExpensesController@paid']);
	Route::post('{id}/delete',['middleware' => ['permission:manage-gymie|manage-expenses|delete-expense'],'uses' => 'ExpensesController@delete']);
	Route::get('/categories',['middleware' => ['permission:manage-gymie|manage-expenseCategories|view-expenseCategory'],'uses' => 'ExpenseCategoriesController@index']);
	Route::get('categories/all',['middleware' => ['permission:manage-gymie|manage-expenseCategories|view-expenseCategory'],'uses' => 'ExpenseCategoriesController@index']);
	Route::get('categories/create',['middleware' => ['permission:manage-gymie|manage-expenseCategories|add-expenseCategory'],'uses' => 'ExpenseCategoriesController@create']);
	Route::post('/categories',['middleware' => ['permission:manage-gymie|manage-expenseCategories|add-expenseCategory'],'uses' => 'ExpenseCategoriesController@store']);
	Route::get('categories/{id}/edit',['middleware' => ['permission:manage-gymie|manage-expenseCategories|edit-expenseCategory'],'uses' => 'ExpenseCategoriesController@edit']);
	Route::post('categories/{id}/update',['middleware' => ['permission:manage-gymie|manage-expenseCategories|edit-expenseCategory'],'uses' => 'ExpenseCategoriesController@update']);
	Route::post('categories/{id}/archive',['middleware' => ['permission:manage-gymie|manage-expenseCategories|delete-expenseCategory'],'uses' => 'ExpenseCategoriesController@archive']);
});

//settings
Route::group(['prefix' => 'settings','middleware' => ['permission:manage-gymie|manage-settings','auth']], function() {
	Route::get('/','SettingsController@show');
	Route::get('edit','SettingsController@edit');
	Route::post('save','SettingsController@save');
});

//User Module with roles & permissions
Route::group(['prefix' => 'user','middleware' => ['permission:manage-gymie|manage-users','auth']], function() {
	//User
	Route::get('/','AclController@userIndex');
	Route::get('create','AclController@createUser');
	Route::post('/','AclController@storeUser');
	Route::get('{id}/edit','AclController@editUser');
	Route::post('{id}/update','AclController@updateUser');
	Route::post('{id}/delete','AclController@deleteUser');

});

Route::group(['prefix' => 'user/role','middleware' => ['permission:manage-gymie|manage-users','auth']], function() {
	//Roles
	Route::get('/','AclController@roleIndex');
	Route::get('create','AclController@createRole');
	Route::post('/','AclController@storeRole');
	Route::get('{id}/edit','AclController@editRole');
	Route::post('{id}/update','AclController@updateRole');
	Route::post('{id}/delete','AclController@deleteRole');

});

Route::group(['prefix' => 'user/permission','middleware' => ['auth','role:Gymie']], function() {
	//Permissions
	Route::get('/','AclController@permissionIndex');
	Route::get('create','AclController@createPermission');
	Route::post('/','AclController@storePermission');
	Route::get('{id}/edit','AclController@editPermission');
	Route::post('{id}/update','AclController@updatePermission');
	Route::post('{id}/delete','AclController@deletePermission');
});
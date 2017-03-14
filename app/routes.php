<?php
  
Route::get('/', array('as'=>'index','uses'=>'IndexController@indexAction'));
Route::get('/report', array('as'=>'index','uses'=>'IndexController@reportAction'));
Route::get('/ajax/content', array('as'=>'ajaxcontent','uses'=>'IndexController@ajaxContentAction'));
Route::post('/ajax/tasks', array('as'=>'ajaxcontentprocess','uses'=>'IndexController@getTasksAction'));
Route::post('/ajax/createPDF', array('as'=>'ajaxdownload','uses'=>'IndexController@createDomPDFAction'));
Route::get('/download', array('as'=>'download','uses'=>'IndexController@downloadAction'));
Route::any('/projects', array('as'=>'projectlists','uses'=>'IndexController@projectListsAction'));
Route::get('/downloadtest', array('as'=>'downloadtest','uses'=>'IndexController@downloadtestAction'));

//need to run first before any quickbooks command
Route::get('/quickbooks', array('as'=>'quickbooks', 'uses'=>'QuickbooksController@indexAction'));

Route::get('/quickbook/oauth', array('as'=>'quickbooks_oauth', 'uses'=>'QuickbooksController@oauthAction'));
Route::get('/quickbook/success', array('as'=>'quickbooks_success', 'uses'=>'QuickbooksController@successAction'));
Route::get('/quickbook/menu', array('as'=>'quickbooks_success', 'uses'=>'QuickbooksController@menuUrlAction'));
Route::get('/quickbooks/addaccount', array('as'=>'quickbooks', 'uses'=>'QuickbooksController@addAccount'));
Route::get('/quickbooks/queryaccount', array('as'=>'quickbooks', 'uses'=>'QuickbooksController@queryAccountRef'));

//for automation
Route::get('/quickbooks/addmembers', array('as'=>'quickbooksmembers', 'uses'=>'QuickbooksController@checkMembersAction'));
Route::get('/quickbooks/monthlybill', array('as'=>'quickbooks_monthly_bill', 'uses'=>'QuickbooksController@computeMonthlyHoursAction'));


//test controller
Route::get('/test', array('as'=>'download','uses'=>'TestController@indexAction'));
Route::get('/quickbooks/test', array('as'=>'quickbooks_oauth', 'uses'=>'QuickbooksController@testAction'));

//members
Route::post('/members/update', array('as'=>'memberrate','uses'=>'MemberController@updateAction'));
Route::get('/members/rates', array('as'=>'memberrate','uses'=>'MemberController@rateAction'));
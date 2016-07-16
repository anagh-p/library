<?php

Route::post('library/getAllBooks', 'BookController@getAllBooks');
Route::post('library/lend', 'BookController@lend');
Route::post('customers/getAllCustomers', 'CustomerController@getAllCustomers');
Route::post('login', 'UserController@login');

//Have to redirect some of the above through this middleware once it's defined..
Route::group(['middleware' => 'loggedIn'], function() {
 
});

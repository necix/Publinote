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

//accueil, identification, deconnexion
Route::get('/', 'AccessController@index');
Route::get('/connecter', 'SessionCASController@connect');
Route::get('/deconnecter', 'SessionCASController@deconnect');
Route::get('/inconnu', function(){ return view('inconnu'); });

//espace étudiant
Route::get('/mes_resultats', 'StudentController@index');
Route::get('/epreuve/{n}', 'StudentController@show')->where('n', '[0-9]+');
Route::get('/parametres', 'StudentController@getParams');
Route::post('/parametres', 'StudentController@postParams');
Route::get('/aide', 'StudentController@help');

Route::post('/volet_epreuve', 'StudentController@testPanel');

//espace tuteur
Route::get('/espace_tuteur', 'TutorController@index');
Route::get('/espace_tuteur/epreuve/{n}', 'TutorController@show')->where('n', '[0-9]+');

//espace admin
Route::get('/espace_admin', 'AdminController@index');

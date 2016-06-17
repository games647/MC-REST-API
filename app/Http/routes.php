<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/uuid/{name}', 'NameController@uuid');
$app->get('/hasPaid/{username}', 'NameController@hasPaid');

$app->get('/properties/{uuid}', 'SkinController@skin');
$app->get('/skin/{uuid}', 'SkinController@skinImage');
$app->get('/avatar/{uuid}', 'SkinController@avatarImage');

$app->get('/domain/{domain}', 'OtherController@domainRecords');

$app->get('/stats', 'StatsController@info');

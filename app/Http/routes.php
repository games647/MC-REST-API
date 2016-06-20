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

//why does this subdomain doesn't work?
//$app->group(['prefix' => 'legacy'], function () use ($app) {
    $app->get('/users/profiles/minecraft/{username}', 'LegacyController@uuid');

    $app->get('/users/profiles/minecraft/{username}/{timestamp}', 'LegacyControler@uuidAtTime');

    $app->get('/user/profiles/{uuid}/names', 'LegacyControler@nameHistory');

    $app->post('/profiles/minecraft', 'LegacyControler@multipleUuid');

    $app->get('/session/minecraft/profile/<uuid>', 'LegacyControler@skin');

    $app->get('/session/minecraft/hasJoined?username={username}&serverId={hash}', 'LegacyControler@hasJoined');
//});

$app->get('/uuid/{name}', 'NameController@uuid');
$app->get('/hasPaid/{username}', 'NameController@hasPaid');

$app->get('/name-history/{uuid}/all', 'NameHistoryController@all');
$app->get('/name-history/{uuid}', 'NameHistoryController@nameHistory');
$app->get('/name-history/{uuid}/{time}', 'NameHistoryController@nameHistoryAt');

$app->get('/properties/{uuid}', 'SkinController@skin');
$app->get('/skin/{uuid}', 'SkinController@skinImage');
$app->get('/avatar/{uuid}', 'SkinController@avatarImage');

$app->get('/domain/{domain}', 'OtherController@domainRecords');

$app->get('/ping/{domain}', 'ServerController@ping');
$app->get('/ping/{domain}/players', 'ServerController@players');
$app->get('/ping/{domain}/icon', 'ServerController@icon');
$app->get('/ping/{domain}/motd', 'ServerController@motd');
$app->get('/ping/{domain}/player-count', 'ServerController@playersCount');

$app->get('/stats', 'StatsController@info');

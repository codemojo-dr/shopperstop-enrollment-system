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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/call/missed', 'CallHandler@missedCall');
$app->post('/call/missed', 'CallHandler@missedCall');

$app->get('/sms/incoming', 'CallHandler@processSMS');
$app->post('/sms/incoming', 'CallHandler@processSMS');

$app->get('/r/{code}', function ($code){
    $meta = app('codemojo.meta');
    $number = $meta->get($code);
    if(empty($number)) {
        return response('Invalid link', 404);
    } else{
        return view('enroll');
    }
});
$app->post('/r/{code}', 'CallHandler@processForm');

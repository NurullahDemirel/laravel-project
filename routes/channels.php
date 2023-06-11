<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('requestResponse', function () {
    return true;
});

Broadcast::channel('privateChannel', function () {
    // Kullanıcının yetkisi varsa özel kanala erişim izni verir
    // return $user !== null;
    return true;
});



Broadcast::routes(['middleware' => 'auth:sanctum']);

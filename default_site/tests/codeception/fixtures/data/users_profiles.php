<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.03.16 17:51
 */

return [
    'admin' => [
        'user_id' => 1,
        'name' => 'AdminUsername',
        'location' => 'AdminLocation',
    ],
    // only this user has filled profile and confirmed status
    // other users will be redirected to /profile or confirmation alert
    'user' => [
        'user_id' => 2,
        'name' => 'UserUsername',
        'location' => 'UserLocation',
    ],
    'notConfirmedUser' => [
        'user_id' => 3,
        'name' => 'NotConfirmedUserUsername',
        'location' => 'NotConfirmedUserLocation',
    ],
    'notFilledProfileUser' => [
        'user_id' => 4,
    ]
];
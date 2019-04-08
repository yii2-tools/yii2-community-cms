<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.03.16 16:54
 */

return [
    'admin' => [
        'id' => 1,
        'username' => 'admin',
        'email' => 'admin@domain.ltd',
        'password_hash' => '$2y$10$VsoCmIw//5xwxNuvQ1BoruQoG98QBgJwODHYAmHV8UADfYn45aXOu',      // 123456
        'auth_key' => '2uvLR1YdNUX0DaMqh-TP2GOZgMYB63JZ',
        'confirmed_at' => time(),
    ],
    'user' => [
        'id' => 2,
        'username' => 'user',
        'email' => 'user@domain.ltd',
        'password_hash' => '$2y$10$VsoCmIw//5xwxNuvQ1BoruQoG98QBgJwODHYAmHV8UADfYn45aXOu',
        'auth_key' => '2uvLR1YdNUX0DaMqh-TP2GOZgMYB63JZ',
        'confirmed_at' => time(),
    ],
    'notConfirmedUser' => [
        'id' => 3,
        'username' => 'notConfirmedUser',
        'email' => 'notConfirmedUser@domain.ltd',
        'password_hash' => '$2y$10$VsoCmIw//5xwxNuvQ1BoruQoG98QBgJwODHYAmHV8UADfYn45aXOu',
        'auth_key' => '2uvLR1YdNUX0DaMqh-TP2GOZgMYB63JZ',
    ],
    'notFilledProfileUser' => [
        'id' => 4,
        'username' => 'filledProfileUser',
        'email' => 'notFilledProfileUser@domain.ltd',
        'password_hash' => '$2y$10$VsoCmIw//5xwxNuvQ1BoruQoG98QBgJwODHYAmHV8UADfYn45aXOu',
        'auth_key' => '2uvLR1YdNUX0DaMqh-TP2GOZgMYB63JZ',
        'confirmed_at' => time(),
    ],
];

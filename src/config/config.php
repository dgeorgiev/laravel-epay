<?php
/**
* Epay Settings
* Created by Daniel Georgiev <me@dgeorgiev.biz>
*/

return [
    'mode' => 'stage',
    'stage' => [
        'submit_url' => 'https://devep2.datamax.bg/ep2/epay2_demo/',
        'client_id' => '',
        'secret' => '',
        'success_url' => '',
        'cancel_url' => ''
    ],
    'prod' => [
        'submit_url' => 'https://epay.bg',
        'client_id' => '',
        'secret' => '',
        'success_url' => '',
        'cancel_url' => '',
    ]
];

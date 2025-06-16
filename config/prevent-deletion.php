<?php

return [
    \App\Models\Enquiry::class => [
        'follow_up' => 'follow-ups',
    ],

    \App\Models\Service::class => [
        'plan'  => 'plans',
    ],

    \App\Models\Member::class => [
        'subscription'   => 'subscriptions',
    ],

    \App\Models\Plan::class => [
        'subscription'   => 'subscriptions',
    ],

    \App\Models\Subscription::class => [
        'invoice' => 'invoices',
    ],
];

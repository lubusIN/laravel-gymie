<?php

return [
    \App\Models\Enquiry::class => ['followUps'],

    \App\Models\Service::class => ['plans'],

    \App\Models\Member::class => ['subscriptions'],

    \App\Models\Plan::class => ['subscriptions'],

    \App\Models\Subscription::class => ['invoices'],
];

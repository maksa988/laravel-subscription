<?php

return [
    'models' => [
        'plan' => \LaravelSubscription\Models\SubscriptionPlan::class,

        'subscriber' => \LaravelSubscription\Models\UserSubscription::class,

        'user' => class_exists(App\Models\User::class) ? App\Models\User::class : App\User::class,

        'coupon' => \LaravelSubscription\Models\Coupon::class,
    ],
];

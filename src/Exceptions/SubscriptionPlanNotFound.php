<?php

namespace LaravelSubscription\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanNotFound extends Exception
{
    /**
     * Create a new SubscriptionPlanNotFound instance.
     *
     * @param $key
     *
     * @return static
     */
    public static function notFound($key)
    {
        return new static($key .' is Subscription Plan not found.');
    }
}

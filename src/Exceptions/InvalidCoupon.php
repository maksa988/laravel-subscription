<?php

namespace LaravelSubscription\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class InvalidCoupon extends Exception
{
    /**
     * Create a new InvalidCoupon instance.
     *
     * @param string $code
     *
     * @return static
     */
    public static function notFound($code)
    {
        return new static($code .' is Coupon not found.');
    }

    /**
     * Create a new InvalidCoupon instance.
     *
     * @param string $code
     * @param int $user_id
     *
     * @return static
     */
    public static function isPersonal($code, $user_id)
    {
        return new static('Coupon ['. $code .'] personal for user with id: ['. $user_id .']');
    }
}

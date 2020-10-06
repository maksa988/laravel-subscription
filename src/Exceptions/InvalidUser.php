<?php

namespace LaravelSubscription\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class InvalidUser extends Exception
{
    /**
     * Create a new InvalidUser instance.
     *
     * @param Model $user
     *
     * @return static
     */
    public static function invalidType($user)
    {
        return new static(class_basename($user) .' is not a ['. config('subscription.models.user') .'].');
    }
}

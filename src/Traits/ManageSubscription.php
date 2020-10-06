<?php

namespace LaravelSubscription\Traits;

use Carbon\Carbon;
use LaravelSubscription\Models\SubscriptionPlan;
use LaravelSubscription\Models\UserSubscription;
use LaravelSubscription\SubscriptionBuilder;

trait ManageSubscription
{
    /**
     * @param SubscriptionPlan|string $key
     * @return SubscriptionBuilder
     *
     * @throws \LaravelSubscription\Exceptions\InvalidUser
     * @throws \LaravelSubscription\Exceptions\SubscriptionPlanNotFound
     */
    public function newSubscription($key)
    {
        return new SubscriptionBuilder($this, $key);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscriptions()
    {
        return $this->hasMany(config('subscription.models.subscriber'))->orderBy('created_at', 'desc');
    }

    /**
     * @return UserSubscription|\Illuminate\Database\Eloquent\Relations\HasOne|object|null
     */
    public function subscription()
    {
        return $this->subscriptions()->active()->trial()->first();
    }

    /**
     * @return bool
     */
    public function onTrial()
    {
        /** @var UserSubscription $subscription */
        $subscription = $this->subscription();

        return $subscription->onTrial();
    }

    /**
     * @return Carbon
     */
    public function trialEndsAt()
    {
        return $this->subscription()->trial_ends_at;
    }

    /**
     * @return bool
     */
    public function subscribed()
    {
        return $this->subscription()->valid();
    }
}

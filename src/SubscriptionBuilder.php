<?php

namespace LaravelSubscription;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use LaravelSubscription\Exceptions\InvalidCoupon;
use LaravelSubscription\Exceptions\InvalidUser;
use LaravelSubscription\Exceptions\SubscriptionPlanNotFound;
use LaravelSubscription\Models\Coupon;
use LaravelSubscription\Models\SubscriptionPlan;
use LaravelSubscription\Traits\HasSubscription;

class SubscriptionBuilder
{
    /**
     * @var SubscriptionPlan
     */
    protected $plan;

    /**
     * @var Model
     */
    protected $user;

    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * @var CarbonInterface|Carbon
     */
    protected $trialEndsAt;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * SubscriptionBuilder constructor.
     * @param Model $user
     * @param SubscriptionPlan|string $key
     * @throws InvalidUser
     * @throws SubscriptionPlanNotFound
     */
    public function __construct($user, $key)
    {
        $this->user($user)
            ->plan($key);
    }

    /**
     * @param $user
     * @return SubscriptionBuilder
     *
     * @throws InvalidUser
     */
    public function user($user)
    {
        if(get_class($user) !== config('subscription.models.user')) {
            throw InvalidUser::invalidType($user);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @param SubscriptionPlan|string $key
     * @return SubscriptionBuilder
     *
     * @throws SubscriptionPlanNotFound
     */
    public function plan(SubscriptionPlan $key)
    {
        if(is_string($key)) {
            $plan = app(config('subscription.models.plan'))->where('key', $key)->first();

            if(! $plan) {
                throw SubscriptionPlanNotFound::notFound($key);
            }
        } else {
            $plan = $key;
        }

        $this->plan = $plan;

        return $this;
    }

    /**
     * @param int $days
     *
     * @return SubscriptionBuilder
     */
    public function trialDays($days)
    {
        $this->trialEndsAt = Carbon::now()->addDays($days);

        return $this;
    }

    /**
     * @param CarbonInterface $date
     *
     * @return SubscriptionBuilder
     */
    public function trialUntil(CarbonInterface $date)
    {
        $this->trialEndsAt = $date;

        return $this;
    }

    /**
     * @param Coupon|string $coupon
     * @return SubscriptionBuilder
     *
     * @throws InvalidCoupon
     */
    public function withCoupon($coupon)
    {
        if(is_string($coupon)) {
            $code = $coupon;
            $coupon = app(config('subscription.models.coupon'))->where('code', $code)->first();

            if(! $coupon) {
                throw InvalidCoupon::notFound($code);
            }
        }

        if($coupon->isPersonal() && $this->user->id !== $coupon->user_id) {
            throw InvalidCoupon::isPersonal($coupon->code, $coupon->user_id);
        }

        $this->coupon = $coupon;

        return $this;
    }

    /**
     * @param array $data
     * @return SubscriptionBuilder
     */
    public function withMetadata(array $data)
    {
        $this->meta = $data;

        return $this;
    }

    public function create($paymentMethod = null)
    {
//        $r
    }
}

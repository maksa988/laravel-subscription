<?php

namespace LaravelSubscription\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class UserSubscription extends Model
{
    /** The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //
    ];

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
    ];

    /**
     * Date casts.
     *
     * @var array
     */
    protected $dates = [
        'updated_at',
        'created_at',
        'starts_at',
        'ends_at',
        'trial_ends_at',
    ];

    /**
     * Appends to JSON.
     *
     * @var array
     */
    protected $appends = [
        //
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        //
    ];

    /**
     * The relationship counts that should be eager loaded on every query.
     *
     * @var array
     */
    protected $withCount = [
        //
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
        'prolong_next_period' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(config('subscription.models.plan'), 'subscription_plan_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('subscription.models.user'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(config('subscription.models.coupon'));
    }

    /**
     * @return bool
     */
    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Determine if the subscription is active, on trial, or within its grace period.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->active() || $this->onTrial() || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     *
     * @return bool
     */
    public function onGracePeriod()
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function active()
    {
        // TODO: Статус оплаты
        return true;
    }

    /**
     * Filter query by on grace period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOnGracePeriod($query)
    {
        $query->whereNotNull('ends_at')->where('ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not on grace period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotOnGracePeriod($query)
    {
        $query->whereNull('ends_at')->orWhere('ends_at', '<=', Carbon::now());
    }

    /**
     * Force the trial to end immediately.
     *
     * This method must be combined with swap, resume, etc.
     *
     * @return $this
     */
    public function skipTrial()
    {
        $this->trial_ends_at = null;

        return $this;
    }

    /**
     * Extend an existing subscription's trial period.
     *
     * @param  \Carbon\CarbonInterface  $date
     * @return $this
     */
    public function extendTrial(CarbonInterface $date)
    {
        if (! $date->isFuture()) {
            throw new InvalidArgumentException("Extending a subscription's trial requires a date in the future.");
        }

        if ($date->greaterThan($this->ends_at)) {
            $this->ends_at = $date;
        }

        $this->trial_ends_at = $date;
        $this->save();

        return $this;
    }

    /**
     * @param bool $force
     * @return $this
     */
    public function cancel($force = false)
    {
        if($this->onTrial()) {
            $this->ends_at = $this->trial_ends_at;
            $this->prolong_next_period = false;
        } else {
            $this->prolong_next_period = false;

            if($force) {
                $this->ends_at = Carbon::now();
            }
        }

        $this->save();

        return $this;
    }

    /**
     * @return $this
     */
    public function cancelNow()
    {
        return $this->cancel(true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeTrial($query)
    {
        $query->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by active.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where(function ($query) {
            $query->whereNull('ends_at')
                ->orWhere(function ($query) {
                    $query->onGracePeriod();
                });
        })->where('starts_at', '<=', Carbon::now());
        // CHECK PAYMENT STATUS
//            ->where('stripe_status', '!=', StripeSubscription::STATUS_INCOMPLETE)
//            ->where('stripe_status', '!=', StripeSubscription::STATUS_INCOMPLETE_EXPIRED)
//            ->where('stripe_status', '!=', StripeSubscription::STATUS_UNPAID);
    }
}

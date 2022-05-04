<?php

namespace MichaelNabil230\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MichaelNabil230\LaravelAnalytics\Observers\IpObserver;
use MichaelNabil230\LaravelAnalytics\Traits\GeneralScopes;
use MichaelNabil230\LaravelAnalytics\Models\SessionVisiter;

class Ip extends Model
{
    use GeneralScopes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'ip_address',
        'country',
        'country_code',
        'city',
        'latitude',
        'longitude',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::observe(IpObserver::class);
    }

    /**
     * Get all of the sessionVisiters for the Ip
     *
     * @return HasMany
     */
    public function sessionVisiters(): HasMany
    {
        return $this->hasMany(SessionVisiter::class);
    }

    /**
     * Get all of the visiters for the user.
     */
    public function visiters()
    {
        return $this->hasManyThrough(Visiter::class, SessionVisiter::class);
    }
}

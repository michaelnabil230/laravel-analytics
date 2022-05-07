<?php

namespace MichaelNabil230\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MichaelNabil230\LaravelAnalytics\Observers\IpObserver;
use MichaelNabil230\LaravelAnalytics\Traits\GeneralScopes;

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
        'additional_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'additional_data' => 'array',
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

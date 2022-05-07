<?php

namespace MichaelNabil230\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MichaelNabil230\LaravelAnalytics\Events;
use MichaelNabil230\LaravelAnalytics\Traits\GeneralScopes;

class SessionVisiter extends Model
{
    use GeneralScopes;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'ip_id',
        'authenticatable_type',
        'authenticatable_id',
        'start_at',
        'end_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,int>
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string>
     */
    protected $appends = [
        'time',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => Events\SavingSessionVisiter::class,
        'saved' => Events\SessionVisiterSaved::class,
        'creating' => Events\CreatingSessionVisiter::class,
        'created' => Events\SessionVisiterCreated::class,
        'updating' => Events\UpdatingSessionVisiter::class,
        'updated' => Events\SessionVisiterUpdated::class,
    ];

    /**
     * Get the ip that owns the SessionVisiter
     *
     * @return BelongsTo
     */
    public function ip(): BelongsTo
    {
        return $this->belongsTo(Ip::class);
    }

    /**
     * Get all of the visiters for the SessionVisiter
     *
     * @return HasMany
     */
    public function visiters(): HasMany
    {
        return $this->hasMany(Visiter::class);
    }

    /**
     * Get the user of the analytics record.
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get time user in the website to the analytics data.
     *
     * @return Attribute
     *
     */
    protected function time(): Attribute
    {
        return Attribute::get(fn () => $this->end_at ? $this->end_at->diff($this->start_at)->format('%H:%I:%S') : null);
    }
}

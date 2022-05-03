<?php

namespace MichaelNabil230\LaravelAnalytics;

use DeviceDetector\DeviceDetector;
use Illuminate\Cache\CacheManager;
use DeviceDetector\Parser\OperatingSystem;
use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Contracts\Auth\Authenticatable;
use MichaelNabil230\LaravelAnalytics\Jobs\GetGeoipData;

class LaravelAnalytics
{
    // Model
    protected $model;

    protected CacheManager|null $cache = null;

    protected bool $enableCache;

    protected int $cacheTtl;

    protected string $driverCache;

    public function __construct()
    {
        $this->model = config('analytics.model', Analytics::class);

        $this->setCacheInstance();
    }

    public function setCacheInstance(): void
    {
        $this->enableCache = config('analytics.cache.enable', true);
        $this->driverCache = config('analytics.cache.driver', 'file');
        $this->cacheTtl = config('analytics.cache.ttl', 15);

        if ($this->enableCache) {
            $this->cache = $this->app['cache']->store($this->driverCache);
        }
    }

    protected function recordVisit($agent = null, string $event = '')
    {
        if ($this->isMatchingPath()) {
            return;
        }

        if (!$this->isValidIp()) {
            return;
        }

        $ip = request()->ip();

        $data = $this->getVisitData($agent ?: request()->userAgent());
        $data['event'] = $event ?: request()->header('X-Event', 'web-routes');

        if ($this->enableCache && !$this->cache?->has("get-data-by-{$ip}")) {
            $data += $this->cache?->get("get-data-by-{$ip}", []);
        }

        $analytics = $this->model::create($data);

        if ($this->enableCache && !$this->cache?->has("get-data-by-{$ip}")) {
            GetGeoipData::dispatch($analytics);
        }

        return $analytics;
    }

    private function isMatchingPath(): bool
    {
        // Get the paths from the config or the middleware
        $paths = $this->getPathsByHost(request()->getHost());

        foreach ($paths as $path) {
            if ($path !== '/') {
                $path = trim($path, '/');
            }

            if (request()->fullUrlIs($path) || request()->is($path)) {
                return true;
            }
        }

        return false;
    }

    private function getPathsByHost(string $host): array
    {
        $paths = config('analytics.ignore_paths', []);

        if (isset($paths[$host])) {
            return $paths[$host];
        }

        return array_filter($paths, function ($path) {
            return is_string($path);
        });
    }

    private function isValidIp(): bool
    {
        $ip = request()->ip();
        $ips = config('analytics.do_not_track_ips', []);

        return in_array($ip, $ips) && IpUtils::checkIp($ip, $ips);
    }

    protected function getVisitData(string $agent): array
    {
        $deviceDetector = new DeviceDetector($agent);
        $deviceDetector->parse();

        // Browser
        $browser = $deviceDetector->getClient('version')
            ? $deviceDetector->getClient('name') . ' ' . $deviceDetector->getClient('version')
            : $deviceDetector->getClient('name');

        $browserFamily = str_replace(' ', '-', strtolower($deviceDetector->getClient('name')));

        // Browser language
        preg_match_all('/([a-z]{2})-[A-Z]{2}/', request()->server('HTTP_ACCEPT_LANGUAGE'), $matches);

        $lang = count($matches) && count($matches[0]) ? $matches[0][0] : '';
        $langFamily = count($matches) && count($matches[1]) ? $matches[1][0] : '';

        // OS
        $os = $deviceDetector->getOs('version')
            ? $deviceDetector->getOs('name') . ' ' . $deviceDetector->getOs('version')
            : $deviceDetector->getOs('name');

        $osFamily = str_replace(
            ' ',
            '-',
            strtolower(OperatingSystem::getOsFamily($deviceDetector->getOs('short_name')))
        );
        $osFamily = $osFamily == 'gnu/linux' ? 'linux' : $osFamily;

        // "UNK UNK" browser and OS
        $browserFamily = ($browser == 'UNK UNK') ? 'unk' : $browserFamily;
        $osFamily = ($os == 'UNK UNK') ? 'unk' : $osFamily;

        // Whether it's a bot
        $bot = null;
        $isBot = $deviceDetector->isBot();
        if ($isBot) {
            $bot = $deviceDetector->getBot();
        } else {
            if (in_array($browserFamily, config('analytics.bot_browsers', []))) {
                $isBot = true;
                $bot = ['name' => $browserFamily];
            }
        }

        $user = $this->getUser();

        return [
            'user_id' => $user ?: $user?->id,
            'ip' => request()->ip(),
            'method' => request()->method(),
            'url' => request()->fullUrl(),
            'referer' => request()->headers->get('referer'),
            'is_ajax' => request()->ajax(),
            'user_agent' => $agent,
            'is_mobile' => $deviceDetector->isMobile(),
            'is_desktop' => $deviceDetector->isDesktop(),
            'is_bot' => $isBot,
            'bot' => $bot ? $bot['name'] : null,
            'os' => $os,
            'os_family' => $osFamily,
            'browser_family' => $browserFamily,
            'browser' => $browser,
            'browser_language_family' => $langFamily,
            'browser_language' => $lang,
        ];
    }

    private function getUser(): Authenticatable|null
    {
        $guards = config('analytics.authentication.guards');

        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            $user = auth()->guard($guard);
            if ($user->check()) {
                return $user->user();
            }
        }
        return null;
    }

    public function __call(string $method, array $parameters): mixed
    {
        return $this->$method(...$parameters);
    }

    public static function __callStatic(string $method, array $parameters): mixed
    {
        return (new static)->$method(...$parameters);
    }
}

<?php

namespace MichaelNabil230\LaravelAnalytics;

use DeviceDetector\DeviceDetector;
use Illuminate\Support\Collection;
use DeviceDetector\Parser\OperatingSystem;
use MichaelNabil230\LaravelAnalytics\Models\Ip;
use MichaelNabil230\LaravelAnalytics\Models\Visiter;
use MichaelNabil230\LaravelAnalytics\Helpers\CheckForIp;
use MichaelNabil230\LaravelAnalytics\Helpers\CheckForPath;
use MichaelNabil230\LaravelAnalytics\Models\SessionVisiter;
use MichaelNabil230\LaravelAnalytics\Services\Authentication;

class LaravelAnalytics
{
    public function recordVisit($agent = null, string $event = ''): ?Visiter
    {
        if (CheckForPath::make(request()->getHost())->getResult()) {
            return null;
        }

        if (CheckForIp::make(request()->ip())->getResult()) {
            return null;
        }

        $ipModel = config('analytics.ip_model', Ip::class);
        $ip = $ipModel::firstOrCreate(['ip_address' => request()->ip()]);

        $data = $this->getVisitData($agent ?: request()->userAgent());

        $sessionVisiter = $this->firstOrCreateSessionVisiter($ip->id, (bool)$data->get('is.bot', false));

        $data = $data->merge([
            'event' => $event ?: request()->header('X-Event', 'web-routes'),
            'session_visiter_id' => $sessionVisiter->id,
        ])->toArray();

        $visiterModel = config('analytics.visiter_model', Visiter::class);
        return $visiterModel::create($data);
    }

    private function firstOrCreateSessionVisiter(string $ipId, bool $isBot): SessionVisiter
    {
        $user = Authentication::getUser();

        $sessionVisiterModel = config('analytics.session_visiter_model', SessionVisiter::class);

        return $sessionVisiterModel::firstOrCreate([
            'ip_id' => $ipId,
            'end_at' => null,
        ], [
            'authenticatable_type' => $user ? get_class($user) : null,
            'authenticatable_id' => $user ?: $user?->id,
            'end_at' => $isBot ? now() : null,
        ]);
    }

    private function getVisitData(string $agent): Collection
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
        $browserFamily = ($browser == 'UNK UNK') ? 'Unknown' : $browserFamily;
        $osFamily = ($os == 'UNK UNK') ? 'Unknown' : $osFamily;

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

        return collect([
            'method' => request()->method(),
            'url' => request()->fullUrl(),
            'referer' => request()->headers->get('referer'),
            'user_agent' => $agent,
            'is' => [
                'ajax' => request()->ajax(),
                'bot' => $isBot,
                'desktop' => $deviceDetector->isDesktop(),
                'mobile' => $deviceDetector->isMobile(),
                'touch' => $deviceDetector->isTouchEnabled(),
            ],
            'bot' => $bot ? $bot['name'] : null,
            'os' => $os,
            'os_family' => $osFamily,
            'browser_family' => $browserFamily,
            'browser' => $browser,
            'browser_language_family' => $langFamily,
            'browser_language' => $lang,
        ]);
    }
}

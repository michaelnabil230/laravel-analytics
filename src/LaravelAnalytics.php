<?php

namespace MichaelNabil230\LaravelAnalytics;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\OperatingSystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MichaelNabil230\LaravelAnalytics\Helpers\CheckForIp;
use MichaelNabil230\LaravelAnalytics\Helpers\CheckForPath;
use MichaelNabil230\LaravelAnalytics\Models\Ip;
use MichaelNabil230\LaravelAnalytics\Models\SessionVisiter;
use MichaelNabil230\LaravelAnalytics\Models\Visiter;
use MichaelNabil230\LaravelAnalytics\Services\Authentication;

class LaravelAnalytics
{
    public function recordVisiter(string $typeRequest, $agent = null): ?Collection
    {
        if (CheckForPath::make(request()->getHost())->getResult()) {
            return null;
        }

        if (CheckForIp::make(request()->ip())->getResult()) {
            return null;
        }

        $ipModel = config('analytics.ip_model', Ip::class);
        $ip = $ipModel::firstOrCreate(['ip_address' => request()->ip()]);

        $data = $this->getVisiterData($agent ?: request()->userAgent());

        $sessionVisiter = $this->firstOrCreateSessionVisiter($ip->id, (bool)$data->get('is.bot', false));

        $data = $data->merge([
            'type_request' => $typeRequest,
            'event' => request()->header('X-Event', ''),
            'event_description' => request()->header('X-Event-Description', ''),
            'session_visiter_id' => $sessionVisiter->id,
        ])->toArray();

        $visiterModel = config('analytics.visiter_model', Visiter::class);
        $visiter = $visiterModel::create($data);

        return $this->formatData($ip, $sessionVisiter, $visiter);
    }

    private function formatData(Ip $ip, SessionVisiter $sessionVisiter, Visiter $visiter): Collection
    {
        $ip = collect($ip)->except(['id', 'created_at', 'updated_at']);
        $sessionVisiter = collect($sessionVisiter)->except(['id', 'ip_id']);
        $visiter = collect($visiter)->except(['id', 'session_visiter_id', 'created_at', 'updated_at']);

        return $ip->merge($sessionVisiter)->merge($visiter);
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
            'authenticatable_id' => $user ? $user->id : null,
            'end_at' => $isBot ? now() : null,
        ]);
    }

    private function getVisiterData(string $agent): Collection
    {
        $deviceDetector = new DeviceDetector($agent);
        $deviceDetector->parse();

        // Browser
        $browser = $deviceDetector->getClient('version')
            ? $deviceDetector->getClient('name') . ' ' . $deviceDetector->getClient('version')
            : $deviceDetector->getClient('name');
        $browserFamily = Str::replaceFirst(' ', '-', Str::lower($deviceDetector->getClient('name')));
        $browserFamily = ($browser == 'UNK UNK') ? 'UNK UNK' : $browserFamily;

        // Browser language
        preg_match_all('/([a-z]{2})-[A-Z]{2}/', request()->server('HTTP_ACCEPT_LANGUAGE'), $matches);

        $lang = count($matches) && count($matches[0]) ? $matches[0][0] : '';
        $langFamily = count($matches) && count($matches[1]) ? $matches[1][0] : '';

        // OS
        $os = $deviceDetector->getOs('version')
            ? $deviceDetector->getOs('name') . ' ' . $deviceDetector->getOs('version')
            : $deviceDetector->getOs('name');

        $osFamily = Str::replaceFirst(' ', '-', Str::lower(OperatingSystem::getOsFamily($deviceDetector->getOs('short_name'))));
        $osFamily = ($osFamily == 'gnu/linux') ? 'linux' : $osFamily;
        $osFamily = ($os == 'UNK UNK') ? 'UNK UNK' : $osFamily;

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

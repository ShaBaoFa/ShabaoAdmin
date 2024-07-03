<?php

declare(strict_types=1);
/**
 * This file is part of web-api.
 *
 * @link     https://blog.wlfpanda1012.com/
 * @github   https://github.com/ShaBaoFa
 * @gitee    https://gitee.com/wlfpanda/web-api
 * @contact  mail@wlfpanda1012.com
 */

namespace App\Listener;

use App\Base\BaseRequest;
use App\Events\AfterLogin;
use App\Helper\Ip2region;
use App\Model\LoginLog;
use App\Model\User;
use App\Service\LoginLogService;
use Exception;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use RedisException;

use function Hyperf\Config\config;

#[Listener]
class LoginListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            AfterLogin::class,
        ];
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedisException
     */
    public function process(object $event): void
    {
        /** @var AfterLogin $event */
        $request = di()->get(BaseRequest::class);
        $service = di()->get(LoginLogService::class);
        $ip2region = di()->get(Ip2region::class);
        $agent = $request->getHeader('user-agent')[0] ?? 'unknown';
        $ip = $request->ip();
        try {
            $ipLocation = $ip2region->search($ip);
        } catch (Exception $e) {
            $ipLocation = '未知';
        }
        $service->save([
            'username' => $event->userinfo['username'],
            'ip' => $ip,
            'ip_location' => $ipLocation,
            'os' => $this->os($agent),
            'browser' => $this->browser($agent),
            'status' => $event->loginStatus ? LoginLog::SUCCESS : LoginLog::FAIL,
            'message' => $event->message,
            'login_time' => date('Y-m-d H:i:s'),
        ]);

        /**
         * 目的 : 确认当前在线用户(user logout 会将token加入blacklist).
         */
        $key = sprintf('%sToken:%s', config('cache.default.prefix'), $event->userinfo['id']);
        $cache = di()->get(Redis::class);
        $cache->del($key);
        ($event->loginStatus && $event->token) && $cache->set($key, $event->token, config('auth.jwt.ttl'));

        if ($event->loginStatus) {
            User::query()->where('id', $event->userinfo['id'])
                ->update(['login_time' => date('Y-m-d H:i:s'), 'login_ip' => $ip]);
        }
    }

    private function os(string $agent): string
    {
        if (stripos($agent, 'win') !== false && preg_match('/nt 6.1/i', $agent)) {
            return 'Windows 7';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 6.2/i', $agent)) {
            return 'Windows 8';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 10.0/i', $agent)) {
            return 'Windows 10';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 11.0/i', $agent)) {
            return 'Windows 11';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 5.1/i', $agent)) {
            return 'Windows XP';
        }
        if (stripos($agent, 'linux') !== false) {
            return 'Linux';
        }
        if (stripos($agent, 'mac') !== false) {
            return 'Mac';
        }
        return 'Unknown';
    }

    /**
     * @param mixed $agent
     */
    private function browser(string $agent): string
    {
        if (stripos($agent, 'MSIE') !== false) {
            return 'MSIE';
        }
        if (stripos($agent, 'Edg') !== false) {
            return 'Edge';
        }
        if (stripos($agent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (stripos($agent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (stripos($agent, 'Safari') !== false) {
            return 'Safari';
        }
        if (stripos($agent, 'Opera') !== false) {
            return 'Opera';
        }
        return 'Unknown';
    }
}

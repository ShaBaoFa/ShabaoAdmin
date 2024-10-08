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
use App\Constants\ErrorCode;
use App\Events\AfterLogin;
use App\Exception\BusinessException;
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
use Xmo\JWTAuth\JWT;

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
        $request = $this->container->get(BaseRequest::class);
        $service = $this->container->get(LoginLogService::class);
        $ip2region = $this->container->get(Ip2region::class);
        $agent = $request->getHeader('user-agent')[0] ?? 'unknown';
        $ip = $request->ip();
        try {
            $ipLocation = $ip2region->search($ip);
        } catch (Exception $e) {
            $ipLocation = '未知';
        }
        $loginLog = [
            'username' => $event->userinfo['username'],
            'ip' => $ip,
            'ip_location' => $ipLocation,
            'os' => $this->os($agent),
            'browser' => $this->browser($agent),
            'status' => $event->loginStatus ? LoginLog::SUCCESS : LoginLog::FAIL,
            'message' => $event->message,
            'login_time' => date('Y-m-d H:i:s'),
        ];

        $service->save($loginLog);

        if ($event->loginStatus && $event->token) {
            # 多点登录情况下，只保存一个key会导致最近时刻登录的用户如果登出之后,则该用户不会出现在在线用户监控列表中
            # 利用 token 设置jwt的jti 来做多点登录token的判断
            $jwt = di()->get(JWT::class);
            $parserData = $jwt->getParserData($event->token);
            $scene = $parserData['jwt_scene'];
            $config = $jwt->getSceneConfig($scene);
            $key = match ((string) $config['login_type']) {
                'sso' => sprintf('%sToken:%s', config('cache.default.prefix'), $event->userinfo['id']),
                'mpop' => sprintf('%sToken:%s:%s', config('cache.default.prefix'), $event->userinfo['id'], $parserData['jti']),
                default => throw new BusinessException(ErrorCode::USER_LOGIN_TYPE_ERROR),
            };
            $redis = di()->get(Redis::class);
            $redis->del($key);
            $redis->set($key, $event->token, config('jwt.ttl'));
        }

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

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

namespace App\Service;

use BackedEnum;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Hyperf\WebSocketServer\Sender;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

use function Hyperf\Config\config;

class WsSenderService
{
    #[Inject]
    protected Sender $sender;

    /**
     * @param mixed $uid
     * @param mixed $message
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function sendByUid(int $uid, string $message): array
    {
        $redis = di()->get(Redis::class);
        $key = sprintf('%sws:uid:%s:fd:*', config('cache.default.prefix'), $uid);
        $fdIds = [];
        $iterator = null;
        while (false !== ($fds = $redis->scan($iterator, $key, 100))) {
            foreach ($fds as $fd) {
                if (preg_match('/:fd:(\d+)(:|$)/', $fd, $match) && Arr::has($match, 1)) {
                    $fdIds[] = $match[1];
                }
            }
            unset($fds);
        }
        var_dump($fdIds);
        $onlineFdIds = [];
        foreach ($fdIds as $fdId) {
            $key = sprintf('%sws:uid:%s:fd:%s', config('cache.default.prefix'), $uid, $fdId);
            if (! $this->sender->push((int) $fdId, $message) && $redis->del($key)) {
                continue;
            }
            $onlineFdIds[] = $fdId;
        }
        return $onlineFdIds;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function kickAndSendByUid(int $uid, string $message): void
    {
        $fdIds = $this->sendByUid($uid, $message);
        foreach ($fdIds as $fdId) {
            go(function () use ($fdId) {
                sleep(1);
                var_dump('before disconnect:' . $fdId);
                $this->sender->disconnect((int) $fdId);
                var_dump('after disconnect:' . $fdId);
            });
        }
    }

    public function handleData(BackedEnum $enum, array $data): string
    {
        return json_encode([
            'event' => $enum->value,
            'message' => $enum->getMessage(),
            'success' => true,
            'data' => $data,
        ]);
    }
}

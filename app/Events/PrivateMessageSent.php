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

namespace App\Events;

use Hyperf\Collection\Arr;

class PrivateMessageSent
{
    public int $sendBy;

    public int $receiveBy;

    public string $content;

    public function __construct(array $payload)
    {
        var_dump(Arr::get($payload, 'receive_by'));
        $this->sendBy = (int) Arr::get($payload, 'send_by');
        $this->receiveBy = (int) Arr::get($payload, 'receive_by');
        $this->content = Arr::get($payload, 'content');
    }

    public function getSendBy(): int
    {
        return $this->sendBy;
    }

    public function getReceiveBy(): int
    {
        return $this->receiveBy;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}

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

class AuditMessageSent
{
    public int $sendBy;

    public array $receiveBy;

    public string $content;

    public function __construct(array $payload)
    {
        $this->sendBy = (int) Arr::get($payload, 'send_by');
        $this->receiveBy = (array) Arr::get($payload, 'receive_by');
        $this->content = Arr::get($payload, 'content');
    }

    public function getSendBy(): int
    {
        return $this->sendBy;
    }

    public function getReceiveBy(): array
    {
        return $this->receiveBy;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}

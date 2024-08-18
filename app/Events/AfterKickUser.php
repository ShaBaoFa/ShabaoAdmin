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

class AfterKickUser
{
    public int $uid;

    public function __construct(array $data)
    {
        $this->uid = (int) Arr::get($data, 'uid');
    }

    public function getUid(): int
    {
        return $this->uid;
    }
}

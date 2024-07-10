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

class Operation
{
    protected array $requestInfo;

    public function __construct(array $requestInfo)
    {
        $this->requestInfo = $requestInfo;
    }

    public function getRequestInfo(): array
    {
        return $this->requestInfo;
    }
}

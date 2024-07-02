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

namespace App\Exception;

use App\Constants\ErrorCodeInterface;
use Qbhy\HyperfAuth\Exception\AuthException as BaseAuthException;
use Throwable;

class AuthException extends BaseAuthException
{
    public function __construct(ErrorCodeInterface $code, ?string $message = null, ?Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = $code->getMessage();
        }

        parent::__construct($message, $code->value, $previous);
    }
}

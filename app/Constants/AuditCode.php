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

namespace App\Constants;

use Hyperf\Constants\Annotation\Constants;

#[Constants]
enum AuditCode: int
{
    /**
     * 正在审核.
     */
    case IN_AUDIT = 1;

    /**
     * 审核通过.
     */
    case PASS = 2;

    /**
     * 审核不通过.
     */
    case NOT_PASS = 3;
}

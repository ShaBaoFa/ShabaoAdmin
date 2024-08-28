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
use Hyperf\Constants\EnumConstantsTrait;

#[Constants]
enum DiskFileShareExpireCode: int
{
    use EnumConstantsTrait;

    // expire_type
    case EXPIRE_TYPE_ONE_DAY = 1;
    case EXPIRE_TYPE_ONE_WEEK = 2;
    case EXPIRE_TYPE_ONE_MONTH = 3;
    case EXPIRE_TYPE_ONE_YEAR = 4;
    case EXPIRE_TYPE_FOREVER = -1;

    // 获得时间戳
    public function getSec(): ?int
    {
        switch ($this) {
            case self::EXPIRE_TYPE_ONE_DAY: return 86400;
                // no break
            case self::EXPIRE_TYPE_ONE_WEEK: return 604800;
            case self::EXPIRE_TYPE_ONE_MONTH: return 2592000;
            case self::EXPIRE_TYPE_ONE_YEAR: return 31536000;
            case self::EXPIRE_TYPE_FOREVER: return null;
        }
        return null;
    }
}

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
enum RegionLevelCode: int
{
    use EnumConstantsTrait;

    case REGION_LEVEL_CODE_PROVINCE = 1;
    case REGION_LEVEL_CODE_CITY = 2;
    case REGION_LEVEL_CODE_DISTRICT = 3;
    case REGION_LEVEL_CODE_STREET = 4;
}

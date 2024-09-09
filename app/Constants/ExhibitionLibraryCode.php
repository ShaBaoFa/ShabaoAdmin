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

use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

enum ExhibitionLibraryCode: int
{
    use EnumConstantsTrait;

    // 战新专区（Strategic emerging industries）
    #[Message('exhibition_library.strategic_emerging_industries')]
    case STRATEGIC_EMERGING_INDUSTRIES = 1;

    // 行业专区
    #[Message('exhibition_library.industry')]
    case INDUSTRY = 2;

    // 主题展会
    #[Message('exhibition_library.theme')]
    case THEME = 3;

    // 专场推荐
    #[Message('exhibition_library.special')]
    case SPECIAL = 4;
}

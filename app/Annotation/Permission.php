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

namespace App\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 用户权限验证注解.
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Permission extends AbstractAnnotation
{
    /**
     * @param null|string $code 权限码
     * @param string $where 过滤条件 为 OR 时，检查有一个通过则全部通过 为 AND 时，检查有一个不通过则全不通过
     */
    public function __construct(public ?string $code = null, public string $where = 'OR')
    {
    }
}

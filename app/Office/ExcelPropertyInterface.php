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

namespace App\Office;

use App\Base\BaseModel;
use Closure;
use Psr\Http\Message\ResponseInterface;

interface ExcelPropertyInterface
{
    public function import(BaseModel $model, ?Closure $closure = null): mixed;

    public function export(string $filename, array|Closure $closure): ResponseInterface;
}

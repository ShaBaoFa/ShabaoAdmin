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

namespace App\Interfaces;

interface DictDataServiceInterface
{
    /**
     * 查询一个字典.
     */
    public function getList(?array $params = null, bool $isScope = false): array;

    /**
     * 查询多个字典.
     */
    public function getLists(?array $params = null): array;
}

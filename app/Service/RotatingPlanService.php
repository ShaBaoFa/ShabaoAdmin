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

namespace App\Service;

use App\Base\BaseService;
use App\Constants\BaseCode;
use App\Dao\RotatingPlanDao;
use Hyperf\Collection\Arr;

class RotatingPlanService extends BaseService
{
    public function __construct(RotatingPlanDao $dao)
    {
        $this->dao = $dao;
    }

    public function index(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        Arr::set($params, '_with', ['theme']);
        return parent::getPageList($params, $isScope);
    }

    public function publicIndex(?array $params = null, bool $isScope = false): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc', 'status' => BaseCode::BASE_NORMAL->value], $params);
        Arr::set($params, '_with', ['theme']);
        return parent::getPageList($params, $isScope);
    }

    public function getPageListByRecycle(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        Arr::set($params, '_with', ['theme']);
        return parent::getPageListByRecycle($params, $isScope);
    }
}

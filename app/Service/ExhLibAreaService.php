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
use App\Constants\ErrorCode;
use App\Dao\ExhLibAreaDao;
use App\Exception\BusinessException;
use Hyperf\Collection\Arr;

class ExhLibAreaService extends BaseService
{
    /**
     * @var ExhLibAreaDao
     */
    public $dao;

    public function __construct(ExhLibAreaDao $dao)
    {
        $this->dao = $dao;
    }

    public function info(mixed $id): array
    {
        $info = $this->find($id)->load('createdBy');
        if (! $info) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        return $info->toArray();
    }

    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        Arr::set($params, '_with', ['createdBy' => ['fields' => ['id', 'nickname']]]);
        return parent::getPageList($params, $isScope);
    }

    public function update($id, array $data): bool
    {
        if (! $this->checkExists(['id' => $id], false)) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        return parent::update($id, $data);
    }
}

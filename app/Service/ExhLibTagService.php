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
use App\Dao\ExhLibTagDao;
use App\Exception\BusinessException;

class ExhLibTagService extends BaseService
{
    /**
     * @var ExhLibTagDao
     */
    public $dao;

    public function __construct(ExhLibTagDao $dao)
    {
        $this->dao = $dao;
    }

    public function info(mixed $id): array
    {
        $info = $this->find($id);
        if (! $info) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        return $info->toArray();
    }

    public function update($id, array $data): bool
    {
        if (! $this->checkExists(['id' => $id], false)) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        return parent::update($id, $data);
    }
}

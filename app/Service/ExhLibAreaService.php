<?php

namespace App\Service;

use App\Base\BaseService;
use App\Constants\ErrorCode;
use App\Dao\ExhLibAreaDao;
use App\Exception\BusinessException;

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
        $info = $this->find($id);
        if (!$info) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        return $info->toArray();
    }

    public function update($id, array $data): bool
    {
        if (! $this->checkExists(['id' => $id],false)) throw new BusinessException(ErrorCode::NOT_FOUND);
        return parent::update($id,$data);
    }
}
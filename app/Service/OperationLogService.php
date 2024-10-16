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
use App\Dao\OperationLogDao;

class OperationLogService extends BaseService
{
    public $dao;

    public function __construct(OperationLogDao $dao)
    {
        $this->dao = $dao;
    }
}

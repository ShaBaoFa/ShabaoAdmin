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
use App\Dao\RegionDao;
use Hyperf\Cache\Annotation\Cacheable;

class RegionService extends BaseService
{
    /**
     * @var RegionDao
     */
    public $dao;

    public function __construct(RegionDao $dao)
    {
        $this->dao = $dao;
    }

    #[Cacheable(prefix: 'RegionService', value: 'getRegion:#{hashKey}', ttl: 3600)]
    public function getRegion(array $params, string $hashKey): array
    {
        return $this->getList($params, false);
    }

    #[Cacheable(prefix: 'RegionService', value: 'regionId:#{id}', ttl: 3600)]
    public function info(mixed $id): array
    {
        return $this->find($id)->toArray();
    }
}

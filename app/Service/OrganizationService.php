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
use App\Dao\OrganizationDao;
use App\Dao\UserDao;
use App\Exception\BusinessException;
use App\Model\Department;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Collection\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function Hyperf\Support\env;

class OrganizationService extends BaseService
{
    /**
     * @var OrganizationDao
     */
    public $dao;

    public function __construct(OrganizationDao $dao)
    {
        $this->dao = $dao;
    }

    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        return parent::getTreeList($params, $isScope);
    }

    public function getTreeListByRecycle(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        return parent::getTreeListByRecycle($params, $isScope);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAllStaff(int $id): array
    {
        $userDao = di()->get(UserDao::class);
        return $userDao->belongThisOrg($id) ? $this->dao->getAllStaff($id) : [];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getRegion(mixed $id): array
    {
        $rs = di()->get(RegionService::class);
        return $rs->info($id);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAllDept(int $id): array
    {
        $userDao = di()->get(UserDao::class);
        return $userDao->belongThisOrg($id) ? $this->dao->getAllDept($id) : [];
    }

    /**
     * 新增组织.
     */
    public function save(array $data): mixed
    {
        return $this->dao->save($this->handleData($data));
    }

    /**
     * 更新组织.
     */
    #[CacheEvict(prefix: 'OrgInfo', value: 'OrgId_#{id}')]
    public function update(mixed $id, array $data): bool
    {
        $handleData = $this->handleData($data);
        if (! $this->checkChildrenExists($id)) {
            return $this->dao->update($id, $handleData);
        }
        $update[] = [
            'id' => $id,
            'data' => $handleData,
        ];
        $descendants = $this->getDescendants(parentId: (int) $id);
        foreach ($descendants as $descendant) {
            $handleDescendantOrganizationLevelData = $this->handleDescendantLevels($descendant['level'], $handleData['level'], $id);
            $update[] = [
                'id' => $descendant['id'],
                'data' => ['level' => $handleDescendantOrganizationLevelData],
            ];
        }
        return $this->dao->batchUpdate($update);
    }

    /**
     * 真实删除部门.
     */
    public function realDel(array $ids): ?array
    {
        // 跳过的
        $ctuIds = [];
        if (count($ids)) {
            foreach ($ids as $id) {
                if (! $this->checkChildrenExists((int) $id) && ! $this->dao->find($id)->users()->exists()) {
                    $this->dao->realDelete([$id]);
                } else {
                    $ctuIds[] = $id;
                }
            }
        }
        return count($ctuIds) ? $this->dao->getOrgName($ctuIds) : null;
    }

    public function info(mixed $id)
    {
        if (! $this->checkExists(['id' => $id], false)) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        return $this->getCacheData($id);
    }

    /**
     * 处理数据.
     */
    protected function handleData(array $data): array
    {
        if (isset($data['role_ids']) && ($key = array_search(env('ADMIN_ROLE'), $data['role_ids'])) !== false) {
            unset($data['role_ids'][$key]);
        }
        $pid = (int) isset($data['parent_id']) ?? 0;
        if (isset($data['id']) && $data['id'] == $pid) {
            throw new BusinessException(ErrorCode::ORG_PARENT_NOT_VALID);
        }
        if ($pid === 0) {
            $data['level'] = $data['parent_id'] = '0';
        } else {
            $parent = $this->find($data['parent_id']);
            /**
             * @var Department $parent
             */
            $data['level'] = $parent->level . ',' . $data['parent_id'];
        }
        Arr::set($data, 'province_region_name', Arr::get($this->getRegion(Arr::get($data, 'province_region_id')), 'name'));
        Arr::set($data, 'city_region_name', Arr::get($this->getRegion(Arr::get($data, 'city_region_id')), 'name'));

        return $data;
    }

    #[Cacheable(prefix: 'OrgInfo', value: 'OrgId_#{id}', ttl: 0)]
    private function getCacheData(int $id): array
    {
        return $this->dao->find($id)->load(['parent'])->toArray();
    }
}

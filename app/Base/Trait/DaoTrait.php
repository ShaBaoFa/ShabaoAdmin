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

namespace App\Base\Trait;

use App\Base\BaseCollection;
use App\Base\BaseModel;
use Hyperf\Collection\Arr;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Tappable\HigherOrderTapProxy;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function App\Helper\user;

trait DaoTrait
{
    /**
     * @var BaseModel
     */
    public $model;

    /**
     * 过滤新增或写入不存在的字段.
     */
    public function filterExecuteAttributes(array &$data, bool $removePk = false): void
    {
        $model = new $this->model();
        $attrs = $model->getFillable();
        foreach ($data as $name => $val) {
            if (! in_array($name, $attrs)) {
                unset($data[$name]);
            }
        }
        if ($removePk && isset($data[$model->getKeyName()])) {
            unset($data[$model->getKeyName()]);
        }

        $model = null;
    }

    /**
     * 新增数据(通过队列).
     */
    public function insertByQueue(array $data): bool
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        return $this->model::insert($data);
    }

    /**
     * 新增数据.
     */
    public function save(array $data): mixed
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    /**
     * 读取一条数据.
     */
    public function find(mixed $id, array $column = ['*']): ?BaseModel
    {
        return ($model = $this->model::find($id, $column)) ? $model : null;
    }

    public function findMany(array $ids, $columns = []): BaseCollection
    {
        return new BaseCollection($this->model::findMany($ids, $columns)->toArray());
    }

    public function findFormCache(mixed $id): ?BaseModel
    {
        return $this->model::findFromCache($id);
    }

    public function findManyFormCache(array $ids): BaseCollection
    {
        return new BaseCollection($this->model::findManyFromCache($ids)->toArray());
    }

    /**
     * 按条件读取一行数据.
     * @return mixed
     */
    public function first(array $condition, array $column = ['*']): ?BaseModel
    {
        return ($model = $this->model::where($condition)->first($column)) ? $model : null;
    }

    /**
     * 获取单个值
     * @return null|HigherOrderTapProxy|mixed|void
     */
    public function value(array $condition, string $columns = 'id')
    {
        return ($model = $this->model::where($condition)->value($columns)) ? $model : null;
    }

    /**
     * 获取单列值
     */
    public function pluck(array $condition, string $columns = 'id', ?string $key = null): array
    {
        return $this->model::where($condition)->pluck($columns, $key)->toArray();
    }

    /**
     * 从回收站读取一条数据.
     * @noinspection PhpUnused
     */
    public function readByRecycle(mixed $id): ?BaseModel
    {
        return ($model = $this->model::withTrashed()->find($id)) ? $model : null;
    }

    /**
     * 单个或批量软删除数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function delete(array $ids): bool
    {
        $this->model::destroy($ids);
        return true;
    }

    /**
     * 更新一条数据.
     */
    public function update(mixed $id, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::find($id)->update($data) > 0;
    }

    /**
     * 按条件更新数据.
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::query()->where($condition)->update($data) > 0;
    }

    /**
     * 单个或批量真实删除数据.
     */
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            $model && $model->forceDelete();
        }
        return true;
    }

    /**
     * 单个或批量从回收站恢复数据.
     */
    public function recovery(array $ids): bool
    {
        $this->model::withTrashed()->whereIn((new $this->model())->getKeyName(), $ids)->restore();
        return true;
    }

    /**
     * 单个或批量禁用数据.
     */
    public function disable(array $ids, string $field = 'status'): bool
    {
        $this->model::query()->whereIn((new $this->model())->getKeyName(), $ids)->update([$field => $this->model::DISABLE]);
        return true;
    }

    /**
     * 单个或批量启用数据.
     */
    public function enable(array $ids, string $field = 'status'): bool
    {
        $this->model::query()->whereIn((new $this->model())->getKeyName(), $ids)->update([$field => $this->model::ENABLE]);
        return true;
    }

    public function getModel(): BaseModel
    {
        return new $this->model();
    }

    /**
     * 获取列表数据.
     */
    public function getList(?array $params, bool $isScope = true): array
    {
        return $this->listQuerySetting($params, $isScope)->get()->toArray();
    }

    /**
     * 获取列表数据（带分页）.
     */
    public function getPageList(?array $params, bool $isScope = true, string $pageName = 'page'): array
    {
        $paginate = $this->listQuerySetting($params, $isScope)->paginate(
            (int) ($params['pageSize'] ?? $this->model::PAGE_SIZE),
            ['*'],
            $pageName,
            (int) ($params[$pageName] ?? 1)
        );
        return $this->setPaginate($paginate, $params);
    }

    /**
     * 设置数据库分页.
     */
    public function setPaginate(LengthAwarePaginatorInterface $paginate, array $params = []): array
    {
        return [
            'items' => method_exists($this, 'handlePageItems') ? $this->handlePageItems($paginate->items(), $params) : $paginate->items(),
            'pageInfo' => [
                'total' => $paginate->total(),
                'currentPage' => $paginate->currentPage(),
                'totalPage' => $paginate->lastPage(),
            ],
        ];
    }

    /**
     * 获取树列表.
     */
    public function getTreeList(
        ?array $params = null,
        bool $isScope = true,
        string $id = 'id',
        string $parentField = 'parent_id',
        string $children = 'children'
    ): array {
        $params['_tree'] = true;
        $params['_tree_pid'] = $parentField;
        $data = $this->listQuerySetting($params, $isScope)->get();
        /**
         * @var BaseCollection $data
         */
        return $data->toTree([], $data[0]->{$parentField} ?? 0, $id, $parentField, $children);
    }

    /**
     * 返回模型查询构造器.
     */
    public function listQuerySetting(?array $params, bool $isScope): Builder
    {
        $query = (($params['recycle'] ?? false) === true) ? $this->model::onlyTrashed() : $this->model::query();

        if ($params['select'] ?? false) {
            $query->select($this->filterQueryAttributes($params['select']));
        }

        $query = $this->handleOrder($query, $params);

        $isScope && $query->userDataScope();

        return $this->handleSearch($query, $params);
    }

    /**
     * 排序处理器.
     */
    public function handleOrder(Builder $query, ?array &$params = null): Builder
    {
        // 对树型数据加个排序
        if (isset($params['_tree'])) {
            $query->orderBy($params['_tree_pid']);
        }
        if ($params['orderBy'] ?? false) {
            if (is_array($params['orderBy'])) {
                foreach ($params['orderBy'] as $key => $order) {
                    $query->orderBy($order, $params['orderType'][$key] ?? 'asc');
                }
            } else {
                $query->orderBy($params['orderBy'], $params['orderType'] ?? 'asc');
            }
        }

        return $query;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query;
    }

    /**
     * 过滤查询字段不存在的属性.
     */
    public function filterQueryAttributes(array $fields, bool $removePk = false): array
    {
        $model = new $this->model();
        $attrs = $model->getFillable();
        foreach ($fields as $key => $field) {
            if (! in_array(trim($field), $attrs) && mb_strpos(str_replace('AS', 'as', $field), 'as') === false) {
                unset($fields[$key]);
            } else {
                $fields[$key] = trim($field);
            }
        }
        if ($removePk && in_array($model->getKeyName(), $fields)) {
            unset($fields[array_search($model->getKeyName(), $fields)]);
        }
        $model = null;
        return (count($fields) < 1) ? ['*'] : $fields;
    }

    /**
     * 数字更新操作.
     */
    public function numberOperation(mixed $id, string $field, int $value): bool
    {
        return $this->find($id)->increment($field, $value) > 0;
    }

    /**
     * 检查是否有子节点.
     */
    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 获取子孙节点.
     */
    public function getDescendants(int $parentId, array $columns = ['*']): array
    {
        $params = ['level' => $parentId];
        return $this->handleSearch($this->model::query(), $params)->get($columns)->toArray();
    }

    public function checkExists(?array $params = null): bool
    {
        return $this->model::query()->where($params)->exists();
    }

    /**
     * 文件归属.
     */
    public function belongMe(array $condition): bool
    {
        return $this->checkExists(Arr::merge($condition, [$this->getModel()->getDataScopeField() => user()->getId()]));
    }
}

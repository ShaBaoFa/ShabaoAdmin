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

namespace App\Dao;

use App\Base\BaseDao;
use App\Model\DiskFileShare;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;

class DiskShareDao extends BaseDao
{
    /**
     * @var DiskFileShare
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = DiskFileShare::class;
    }

    #[Transactional]
    public function save($data): array
    {
        $items = $data['items'];
        $shared_with = $data['shared_with'] ?? [];
        $shareId = parent::save($data);
        // 只取ID,减少数据库回表操作
        $share = $this->find($shareId, ['id', 'name', 'share_link', 'share_password']);
        /**
         * @var DiskFileShare $share
         */
        $share->diskFiles()->sync(array_unique($items));
        ! empty($shared_with) && $share->shareWith()->sync(array_unique($shared_with));
        return $share->toArray();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            $name = Arr::get($params, 'name'),
            fn (Builder $query) => $query->where('name', 'like', '%' . $name . '%')
        );

        $query->when(
            Arr::get($params, 'created_at'),
            function (Builder $query, $createdAt) {
                if (is_array($createdAt) && count($createdAt) === 2) {
                    $query->whereBetween(
                        'created_at',
                        [$createdAt[0] . ' 00:00:00', $createdAt[1] . ' 23:59:59']
                    );
                }
            }
        );

        return $query;
    }
}

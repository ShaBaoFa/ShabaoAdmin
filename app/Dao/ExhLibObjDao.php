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
use App\Model\ExhLibObj;
use Hyperf\Collection\Arr;
use Hyperf\DbConnection\Annotation\Transactional;

class ExhLibObjDao extends BaseDao
{
    /**
     * @var ExhLibObj
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = ExhLibObj::class;
    }

    #[Transactional]
    public function save(array $data): mixed
    {
        $tags = Arr::get($data, 'tags', []);
        $files = Arr::get($data, 'files', []);
        $covers = Arr::get($data, 'covers', []);
        $share_regions = Arr::get($data, 'share_regions', []);
        $this->filterExecuteAttributes($data, true);
        $obj = $this->model::create($data);
        if (! empty($tags)) {
            $obj->tags()->sync($tags);
        }
        if (! empty($files)) {
            $obj->files()->sync($files);
        }
        if (! empty($covers)) {
            $obj->covers()->sync($covers);
        }
        if (! empty($share_regions)) {
            $obj->share_regions()->sync($share_regions);
        }
        return $obj->{$obj->getKeyName()};
    }
}

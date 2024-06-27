<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Service\Trait;

use App\Dao\BaseDao;
use Hyperf\DbConnection\Db;

trait ServiceTrait
{
    /**
     * @var BaseDao
     */
    public $dao;

    /**
     * 新增数据.
     */
    public function save(array $data): mixed
    {
        return $this->dao->save($data);
    }

    public function batchSave(array $collects): bool
    {
        return Db::transaction(function () use ($collects) {
            foreach ($collects as $collect) {
                $this->dao->save($collect);
            }
            return true;
        });
    }
}

<?php

namespace App\Dao;

use App\Base\BaseDao;
use App\Model\ExhLibTag;

class ExhLibTagDao extends BaseDao
{
    /**
     * @var ExhLibTag
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = ExhLibTag::class;
    }
}
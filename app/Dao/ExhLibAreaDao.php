<?php

namespace App\Dao;

use App\Base\BaseDao;
use App\Model\ExhLibArea;

class ExhLibAreaDao extends BaseDao
{
    /**
     * @var ExhLibArea
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = ExhLibArea::class;
    }
}
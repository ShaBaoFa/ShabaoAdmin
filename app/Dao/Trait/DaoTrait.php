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

namespace App\Dao\Trait;

use App\Model\BaseModel;

trait DaoTrait
{
    /**
     * @var BaseModel
     */
    public $model;

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

    public function save(array $data): mixed
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    public function getModel(): BaseModel
    {
        return new $this->model();
    }
}

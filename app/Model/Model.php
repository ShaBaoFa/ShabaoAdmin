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

namespace App\Model;

use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\ModelCache\Cacheable;

class Model extends BaseModel
{
    use Cacheable;

    /**
     * 隐藏的字段列表.
     * @var string[]
     */
    protected array $hidden = ['deleted_at'];

    protected string $dataScopeField = 'created_by';

    public function save(array $options = []): bool
    {
        return parent::save($options);
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        return parent::update($attributes, $options);
    }

    public function getDataScopeField(): string
    {
        return $this->dataScopeField;
    }

    public function setDataScopeField(string $name): self
    {
        $this->dataScopeField = $name;
        return $this;
    }
}

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

namespace App\Base;

use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;

class BaseModel extends Model
{
    use Cacheable;

    /**
     * 状态
     */
    public const ENABLE = 1;

    public const DISABLE = 2;

    /**
     * 默认每页记录数.
     */
    public const PAGE_SIZE = 15;

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

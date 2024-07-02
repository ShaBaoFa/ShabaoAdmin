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

use App\Base\Trait\DaoTrait;
use Hyperf\Context\Context;

abstract class BaseDao
{
    use DaoTrait;

    /**
     * @var BaseModel|callable-string
     */
    public $model;

    public function __construct()
    {
        $this->assignModel();
    }

    /**
     * 魔术方法，从类属性里获取数据.
     * @return mixed|string
     */
    public function __get(string $name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    abstract public function assignModel();

    /**
     * 把数据设置为类属性.
     */
    public static function setAttributes(array $data): void
    {
        Context::set('attributes', $data);
    }

    /**
     * 获取数据.
     */
    public function getAttributes(): array
    {
        return Context::get('attributes', []);
    }
}

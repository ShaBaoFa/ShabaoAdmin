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

namespace App\Events;

use Hyperf\DbConnection\Model\Model;
use League\Flysystem\Filesystem;

class RealDeleteUploadFile
{
    protected Model $model;

    protected bool $confirm = true;

    protected Filesystem $filesystem;

    public function __construct(Model $model, Filesystem $filesystem)
    {
        $this->model = $model;
        $this->filesystem = $filesystem;
    }

    /**
     * 获取当前模型实例.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * 获取文件处理系统
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * 是否删除.
     */
    public function getConfirm(): bool
    {
        return $this->confirm;
    }

    public function setConfirm(bool $confirm): void
    {
        $this->confirm = $confirm;
    }
}

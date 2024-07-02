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

use App\Office\Excel\PhpOffice;
use App\Office\Excel\XlsWriter;
use Closure;
use Hyperf\Database\Model\Collection;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use function Hyperf\Config\config;

class BaseCollection extends Collection
{
    /**
     * 系统菜单转前端路由树.
     */
    public function menuToRouterTree(): array
    {
        $data = $this->toArray();
        if (empty($data)) {
            return [];
        }

        $routers = [];
        foreach ($data as $menu) {
            $routers[] = $this->setRouter($menu);
        }
        return $this->toTree($routers);
    }

    public function setRouter(&$menu): array
    {
        /**
         * 菜单类型, (M菜单 B按钮 L链接 I iframe).
         */
        $route = ($menu['type'] == 'L' || $menu['type'] == 'I') ? $menu['route'] : '/' . $menu['route'];
        return [
            'id' => $menu['id'],
            'parent_id' => $menu['parent_id'],
            'name' => $menu['code'],
            'component' => $menu['component'],
            'path' => $route,
            'redirect' => $menu['redirect'],
            'meta' => [
                'type' => $menu['type'],
                'icon' => $menu['icon'],
                'title' => $menu['name'],
                'hidden' => ($menu['is_hidden'] === 1),
                'hiddenBreadcrumb' => false,
            ],
        ];
    }

    public function toTree(array $data = [], int $parentId = 0, string $id = 'id', string $parentField = 'parent_id', string $children = 'children'): array
    {
        $data = $data ?: $this->toArray();

        if (empty($data)) {
            return [];
        }

        $tree = [];

        foreach ($data as $value) {
            if ($value[$parentField] == $parentId) {
                $child = $this->toTree($data, $value[$id], $id, $parentField, $children);
                if (! empty($child)) {
                    $value[$children] = $child;
                }
                $tree[] = $value;
            }
        }
        /**
         * 帮助PHP垃圾回收器尽快释放这部分内存.
         */
        unset($data);
        return $tree;
    }

    /**
     * 导出数据.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function export(string $dto, string $filename, null|array|Closure $closure = null, ?Closure $callbackData = null): ResponseInterface
    {
        $excelDrive = config('base-common.excel_drive');
        if ($excelDrive === 'auto') {
            $excel = extension_loaded('xlswriter') ? new XlsWriter($dto) : new PhpOffice($dto);
        } else {
            $excel = $excelDrive === 'xlsWriter' ? new XlsWriter($dto) : new PhpOffice($dto);
        }
        return $excel->export($filename, is_null($closure) ? $this->toArray() : $closure, $callbackData);
    }

    /**
     * 数据导入.
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function import(string $dto, BaseModel $model, ?Closure $closure = null): mixed
    {
        $excelDrive = config('base-common.excel_drive');
        if ($excelDrive === 'auto') {
            $excel = extension_loaded('xlswriter') ? new XlsWriter($dto) : new PhpOffice($dto);
        } else {
            $excel = $excelDrive === 'xlsWriter' ? new XlsWriter($dto) : new PhpOffice($dto);
        }
        return $excel->import($model, $closure);
    }
}

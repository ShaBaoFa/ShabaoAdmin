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

namespace App\Office;

use App\Base\BaseResponse;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Interfaces\DictDataServiceInterface;
use App\Interfaces\ModelExcelInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RedisException;

abstract class BaseExcel
{
    public const ANNOTATION_NAME = 'App\Annotation\ExcelProperty';

    protected ?array $annotationMate;

    protected array $property = [];

    protected array $dictData = [];

    public function __construct(string $dto)
    {
        if (! (new $dto()) instanceof ModelExcelInterface) {
            throw new BusinessException(ErrorCode::DTO_NOT_IMPLEMENT_MODEL_EXCEL);
        }
        $dtoObject = new $dto();
        if (method_exists($dtoObject, 'dictData')) {
            $this->dictData = $dtoObject->dictData();
        }
        $this->annotationMate = AnnotationCollector::get($dto);
        $this->parseProperty();
    }

    public function getProperty(): array
    {
        return $this->property;
    }

    public function getAnnotationInfo(): array
    {
        return $this->annotationMate;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    protected function parseProperty(): void
    {
        if (empty($this->annotationMate) || ! isset($this->annotationMate['_c'])) {
            throw new BusinessException(ErrorCode::DTO_ANNOTATION_INFO_EMPTY);
        }

        foreach ($this->annotationMate['_p'] as $name => $mate) {
            $this->property[$mate[self::ANNOTATION_NAME]->index] = [
                'name' => $name,
                'value' => $mate[self::ANNOTATION_NAME]->value,
                'width' => $mate[self::ANNOTATION_NAME]->width ?? null,
                'align' => $mate[self::ANNOTATION_NAME]->align ?? null,
                'headColor' => $mate[self::ANNOTATION_NAME]->headColor ?? null,
                'headBgColor' => $mate[self::ANNOTATION_NAME]->headBgColor ?? null,
                'color' => $mate[self::ANNOTATION_NAME]->color ?? null,
                'bgColor' => $mate[self::ANNOTATION_NAME]->bgColor ?? null,
                'dictData' => $mate[self::ANNOTATION_NAME]->dictData,
                'dictName' => empty($mate[self::ANNOTATION_NAME]->dictName) ? null : $this->getDictData($mate[self::ANNOTATION_NAME]->dictName),
                'path' => $mate[self::ANNOTATION_NAME]->path ?? null,
            ];
        }
        ksort($this->property);
    }

    /**
     * 下载excel.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function downloadExcel(string $filename, string $content): ResponseInterface
    {
        return di()->get(BaseResponse::class)->getResponse()
            ->withHeader('access-control-expose-headers', 'content-disposition')
            ->withHeader('content-description', 'File Transfer')
            ->withHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('content-disposition', "attachment; filename={$filename}; filename*=UTF-8''" . rawurlencode($filename))
            ->withHeader('content-transfer-encoding', 'binary')
            ->withHeader('pragma', 'public')
            ->withBody(new SwooleStream($content));
    }

    /**
     * 获取字典数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    protected function getDictData(string $dictName): array
    {
        $data = [];
        foreach (di()
            ->get(DictDataServiceInterface::class)
            ->getList(['code' => $dictName]) as $item) {
            $data[$item['key']] = $item['title'];
        }

        return $data;
    }

    /**
     * 获取 excel 列索引.
     */
    protected function getColumnIndex(int $columnIndex = 0): string
    {
        if ($columnIndex < 26) {
            return chr(65 + $columnIndex);
        }
        if ($columnIndex < 702) {
            return chr(64 + intval($columnIndex / 26)) . chr(65 + $columnIndex % 26);
        }
        return chr(64 + intval(($columnIndex - 26) / 676)) . chr(65 + intval((($columnIndex - 26) % 676) / 26)) . chr(65 + $columnIndex % 26);
    }
}

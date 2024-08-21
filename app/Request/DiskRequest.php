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

namespace App\Request;

use App\Base\BaseFormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

class DiskRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 上传文件验证规则.
     * @return string[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function saveFileRules(): array
    {
        return [
            'files' => 'required|array',
            'files.*.hash' => 'required|string|min:32|max:32|exists:upload_files,hash',
            'files.*.parent_id' => 'nullable|int|exists:disk_files,id',
            'files.*.name' => 'required|string|max:30',
        ];
    }

    /**
     * 上传图片验证规则.
     * @return string[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getDownloadTokenRules(): array
    {
        return [
            'file_hashes' => 'required|array',
            'file_hashes.*' => 'required|string|min:32|max:32|exists:upload_files,hash',
        ];
    }

    public function saveFolderRules(): array
    {
        return [
            'parent_id' => 'nullable|int|exists:disk_files,id',
            'name' => 'required|string|max:30',
        ];
    }

    public function listRules(): array
    {
        return [
            'parent_id' => 'nullable|int|exists:disk_files,id',
        ];
    }

    public function getUploaderStsTokenRules(): array
    {
        return [
            'hash' => 'required|string|min:32|max:32|exists:upload_files,hash',
        ];
    }

    public function uploaderCallbackRules(): array
    {
        return [
            'hash' => 'required|string|min:32|max:32|exists:upload_files,hash',
        ];
    }

    public function uploaderPreparationRules(): array
    {
        return [
            'metadata' => 'required|array',
            'metadata.origin_name' => 'required|string|max:255',
            'metadata.size_byte' => 'required|int',
            'metadata.mime_type' => 'required|string|max:255',
            'metadata.last_modified' => 'required|string|max:255',
            'path' => 'max:30',
        ];
    }

    public function getDownLoaderStsTokenRules(): array
    {
        return [
            'hash' => 'required|string|min:32|max:32|exists:upload_files,hash',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'images' => '图片',
            'files' => '文件',
            'path' => '地址',
        ];
    }

    private function getImagesMimes(): string
    {
        return 'jpg,jpeg,png,gif,svg,bmp';
    }

    private function getFilesMines(): string
    {
        return 'txt,doc,docx,xls,xlsx,ppt,pptx,rar,zip,7z,gz,pdf,wps,md';
    }
}

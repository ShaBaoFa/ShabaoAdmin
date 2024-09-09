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
use App\Constants\DiskFileCode;
use Hyperf\Validation\Rule;
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
            'hashes' => 'required|array',
            'hashes.*' => 'required|string',
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

    public function renameRules(): array
    {
        return [
            'new_name' => 'required|string|max:30',
        ];
    }

    public function moveRules(): array
    {
        return [
            'items' => 'required|array',
            'items.*' => 'required|int|exists:disk_files,id',
            'target_folder_id' => 'nullable|int|exists:disk_files,id',
        ];
    }

    public function deleteRules(): array
    {
        return [
            'items' => 'required|array',
            'items.*' => 'required|int|exists:disk_files,id',
        ];
    }

    public function realDeleteRules(): array
    {
        return [
            'items' => 'required|array',
            'items.*' => 'required|int|exists:disk_files,id',
        ];
    }

    public function recoveryRules(): array
    {
        return [
            'items' => 'required|array',
            'items.*' => 'required|int|exists:disk_files,id',
        ];
    }

    public function copyRules(): array
    {
        return [
            'items' => 'required|array',
            'items.*' => 'required|int|exists:disk_files,id',
            'target_folder_id' => 'nullable|int|exists:disk_files,id',
        ];
    }

    public function searchRules(): array
    {
        return [
            'query' => 'required|array',
            'query.name' => 'nullable|string',
            'query.file_type' => ['nullable', 'integer:strict', Rule::in($this->getFileType())],
        ];
    }

    public function folderMeta(): array
    {
        return [
            'path' => 'string',
            'id' => 'integer|exists:disk_files,id',
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
            'files.*.hash' => '文件hash',
            'hashes.*' => '文件hash',
            'files.*.parent_id' => '文件夹',
            'files.*.name' => '文件名',
            'items.*' => '文件或文件夹',
            'new_name' => '新名字',
            'target_folder_id' => '目标文件夹',
            'parent_id' => '文件夹',
            'expire_type' => '过期时间',
            'password' => '密码',
            'query.file_type' => '文件类型',
            'query.name' => '搜索名称',
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

    private function getFileType(): array
    {
        return [
            DiskFileCode::FILE_TYPE_AUDIO->value,
            DiskFileCode::FILE_TYPE_IMAGE->value,
            DiskFileCode::FILE_TYPE_VIDEO->value,
            DiskFileCode::FILE_TYPE_DOCUMENT->value,
            DiskFileCode::FILE_TYPE_OTHER->value,
        ];
    }
}

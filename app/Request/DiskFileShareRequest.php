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
use App\Constants\DiskFileShareExpireCode;
use App\Constants\DiskFileSharePermissionCode;
use Hyperf\Validation\Rule;

class DiskFileShareRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function saveRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'permission' => ['int', Rule::in($this->getSharePermission())],
            'items' => 'required|array',
            'items.*' => 'required|int|exists:disk_files,id',
            'shared_with' => 'nullable|array',
            'shared_with.*' => 'int|exists:users,id',
            'expire_type' => ['int', Rule::in($this->getExpireType())],
            'password' => ['required', 'alpha_num:ascii', 'min:4', 'max:4'],
        ];
    }

    public function shareLinkRules(): array
    {
        return [
            'share_password' => ['required', 'alpha_num:ascii', 'min:4', 'max:4'],
            'share_link' => ['required', 'string', 'exists:disk_file_shares,share_link'],
            'parent_id' => ['int', 'exists:disk_files:id'],
        ];
    }

    public function getShareDownloadTokenRule(): array
    {
        return [
            'hashes' => ['required', 'array'],
            'hashes.*' => 'required|string|min:32|max:32|exists:upload_files,hash',
            'share_link' => ['required', 'string', 'min:16', 'max:16', 'exists:disk_file_shares,share_link'],
            'share_password' => ['required', 'alpha_num:ascii', 'min:4', 'max:4'],
        ];
    }

    public function getFolderHashRule(): array
    {
        return [
            'hashes' => ['required', 'array'],
            'hashes.*' => 'required|string|min:32|max:32|exists:upload_files,hash',
            'share_link' => ['required', 'string', 'min:16', 'max:16', 'exists:disk_file_shares,share_link'],
            'share_password' => ['required', 'alpha_num:ascii', 'min:4', 'max:4'],
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'items.*' => '文件或文件夹',
            'name' => '分享包名称',
            'expire_type' => '过期时间',
            'permission' => '分享权限',
            'password' => '密码',
            'shared_with' => '分享对象',
        ];
    }

    private function getExpireType(): array
    {
        return [
            DiskFileShareExpireCode::EXPIRE_TYPE_ONE_DAY->value,
            DiskFileShareExpireCode::EXPIRE_TYPE_ONE_MONTH->value,
            DiskFileShareExpireCode::EXPIRE_TYPE_ONE_WEEK->value,
            DiskFileShareExpireCode::EXPIRE_TYPE_ONE_YEAR->value,
            DiskFileShareExpireCode::EXPIRE_TYPE_FOREVER->value,
        ];
    }

    private function getSharePermission(): array
    {
        return [
            DiskFileSharePermissionCode::DOWNLOAD->value,
            DiskFileSharePermissionCode::ONLY_READ->value,
        ];
    }
}

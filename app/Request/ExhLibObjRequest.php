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

class ExhLibObjRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
            'ids' => ['array'],
            'ids.*' => ['integer', 'exists:exh_lib_objs,id'],
            'id' => ['integer', 'exists:exh_lib_objs,id'],
        ];
    }

    /**
     * 目录数据验证规则
     * return array.
     */
    public function indexRules(): array
    {
        return [
            'type' => ['nullable', 'integer', 'min:1', 'max:3'],
            'lib_type' => ['nullable', 'integer', 'min:1', 'max:4'],
            'lib_area_type' => ['nullable', 'integer', 'min:1', 'max:4'],
            'title' => ['nullable', 'string'],
        ];
    }

    public function saveRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'author' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'telephone_number'],
            'email' => ['required', 'string', 'email'],
            'profile' => ['required'],
            'save_dir_id' => ['required', 'integer'],
            'tags' => ['required', 'array'],
            'tags.*' => ['integer', 'exists:exh_lib_tags,id'],
            'type' => ['required', 'integer', 'min:1', 'max:3'], // (1虚拟展项素材 2实体展项素材 3平台展项素材)
            'lib_type' => ['required', 'integer', 'min:1', 'max:4'], // (1战新 2行业 3主题 4专场)
            'lib_area_type' => ['required', 'integer', 'exists:exh_lib_areas,id'],
            'files' => ['required', 'array'],
            'files.*' => ['string', 'exists:upload_files,hash'],
            'covers' => ['required', 'array'],
            'covers.*' => ['string', 'exists:upload_files,hash'],
            'share_regions' => ['required_if:type,2', 'array'],
            'share_regions.*' => ['integer', 'exists:regions,id'],
            'redirect_url' => ['required_if:type,3', 'url'],
        ];
    }

    public function updateRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'author' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'telephone_number'],
            'email' => ['required', 'string', 'max:100', 'email'],
            'profile' => ['required'],
            'tags' => ['required', 'array'],
            'tags.*' => ['integer', 'exists:exh_lib_tags,id'],
            'type' => ['required', 'integer', 'min:1', 'max:3'], // (1虚拟展项素材 2实体展项素材 3平台展项素材)
            'lib_type' => ['required', 'integer', 'min:1', 'max:4'], // (1战新 2行业 3主题 4专场)
            'lib_area_type' => ['required', 'integer', 'exists:exh_lib_areas,id'],
            'files' => ['required', 'array'],
            'files.*' => ['string', 'exists:upload_files,hash'],
            'covers' => ['required', 'array'],
            'covers.*' => ['string', 'exists:upload_files,hash'],
            'share_regions' => ['require_if:type,2', 'array'],
            'share_regions.*' => ['integer', 'exists:regions,id'],
            'redirect_url' => ['url', 'require_if:type,3'],
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '展项',
            'ids.*' => '展项',
            'title' => '标题',
            'author' => '作者',
            'phone' => '电话',
            'email' => '邮箱',
            'profile' => '简介',
            'save_dir_id' => '保存文件夹',
            'tags' => '标签',
            'tags.*' => '标签',
            'type' => '素材类型',
            'lib_type' => '专区类型',
            'lib_area_type' => '子专区类型',
            'files' => '文件',
            'files.*' => '文件',
            'covers' => '封面',
            'covers.*' => '封面',
            'region_id' => '地区',
            'redirect_url' => '跳转链接',
        ];
    }
}

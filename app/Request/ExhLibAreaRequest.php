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
use App\Constants\ExhibitionLibraryCode;
use Hyperf\Validation\Rule;

class ExhLibAreaRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
            'ids' => ['array'],
            'ids.*' => ['integer', 'exists:exh_lib_areas,id'],
            'id' => ['integer', 'exists:exh_lib_areas,id'],
            'name' => ['string', 'max:20'],
            'icon' => ['string', 'max:20'],
            'lib_type' => ['integer', Rule::in([ExhibitionLibraryCode::STRATEGIC_EMERGING_INDUSTRIES->value, ExhibitionLibraryCode::INDUSTRY->value, ExhibitionLibraryCode::THEME->value, ExhibitionLibraryCode::SPECIAL->value])],
        ];
    }

    /**
     * 目录数据验证规则
     * return array.
     */
    public function indexRules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function saveRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'icon' => ['required_unless:lib_type:' . ExhibitionLibraryCode::SPECIAL->value, 'string', 'max:20'],
            'profile' => ['string'],
            'lib_type' => ['required', 'integer', Rule::in([ExhibitionLibraryCode::STRATEGIC_EMERGING_INDUSTRIES->value, ExhibitionLibraryCode::INDUSTRY->value, ExhibitionLibraryCode::THEME->value, ExhibitionLibraryCode::SPECIAL->value])],
        ];
    }
}

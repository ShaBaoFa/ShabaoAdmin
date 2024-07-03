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
use JetBrains\PhpStorm\ArrayShape;

class AuthRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function registerRules(): array
    {
        return [
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string',
        ];
    }

    public function loginRules(): array
    {
        return [
            'username' => 'required|string|exists:users,username',
            'password' => 'required|string',
        ];
    }

    #[ArrayShape([
        'username' => 'string',
        'password' => 'string',
    ])]
    public function commonAttributes(): array
    {
        return [
            'username' => '用户名',
            'password' => '密码',
        ];
    }
}

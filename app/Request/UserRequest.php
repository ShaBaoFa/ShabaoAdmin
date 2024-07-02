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

use App\Request\Rules\PasswordRule;
use JetBrains\PhpStorm\ArrayShape;

class UserRequest extends FormRequest
{
    public function saveRules(): array
    {
        $passwordRule = di()->get(PasswordRule::class);
        return [
            'username' => ['required', 'string', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', $passwordRule],
            //            'role_ids' => ['required', 'array'],
            //            'dept_ids' => ['required', 'array'],
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

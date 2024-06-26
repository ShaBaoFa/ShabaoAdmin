<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
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

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

use JetBrains\PhpStorm\ArrayShape;

class AuthRequest extends FormRequest
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

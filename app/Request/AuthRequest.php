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

class AuthRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function registerRules(): array
    {
        return [
            'account' => 'required|string|unique:users,account',
            'password' => 'required|string',
        ];
    }

    public function loginRules(): array
    {
        return [
            'account' => 'required|string|exists:users,account',
            'password' => 'required|string',
        ];
    }
}

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

namespace App\Request\Rules;

use Hyperf\Validation\Contract\Rule;

/**
 * @property string $mes
 */
class PasswordRule implements Rule
{
    public function __construct()
    {
        $this->mes = '';
    }

    public function passes($attribute, $value): bool
    {
        var_dump($attribute, $value);
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[~!@#$%^&*()_+`\-={}:";\'<>?,.\/]).{6,}$/';
        if (! preg_match($pattern, $value)) {
            $this->mes = '密码必须包含大小写字母、数字、特殊字符，且长度不小于6位';
            return false;
        }
        return true;
    }

    public function message(): array|string
    {
        return $this->mes;
    }
}

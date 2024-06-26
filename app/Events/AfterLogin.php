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

namespace App\Events;

class AfterLogin
{
    public array $userinfo;

    public bool $loginStatus = false;

    public string $message;

    public string $token;

    public function __construct(array $userinfo)
    {
        $this->userinfo = $userinfo;
    }
}

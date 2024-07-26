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

namespace App\Events;

use App\Dao\UserDao;
use Hyperf\Di\Annotation\Inject;

use function Hyperf\Config\config;

class AfterOrgSave
{
    public int $orgId;

    public array $data;

    #[Inject]
    protected UserDao $userDao;

    public function __construct(int $orgId, $data)
    {
        $this->orgId = $orgId;
        $this->data = $data;
    }

    public function getOrganizationId(): int
    {
        return $this->orgId;
    }

    public function getOrgAdminInfo(): array
    {
        $role_ids = $data['role_ids'] ?? [];
        $role_ids = array_merge($role_ids, [config('base-common.org_super_role_id')]);
        return [
            'username' => $this->data['username'],
            'password' => config('base-common.org_default_password'),
            'role_ids' => $role_ids,
            'org_id' => $this->getOrganizationId(),
        ];
    }
}

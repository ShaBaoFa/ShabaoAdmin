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

namespace App\Listener;

use App\Events\AfterOrgSave;
use App\Service\UserService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerInterface;

#[Listener]
class OrgSaveListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            AfterOrgSave::class,
        ];
    }

    public function process(object $event): void
    {
        /**
         * @var AfterOrgSave $event
         */
        $orgAdminInfo = $event->getOrgAdminInfo();
        $userService = di()->get(UserService::class);
        $userService->save($orgAdminInfo);
    }
}

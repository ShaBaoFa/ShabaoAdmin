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

namespace App\Aspect;

use App\Annotation\Permission;
use App\Base\BaseRequest;
use App\Constants\ErrorCode;
use App\Exception\NoPermissionException;
use App\Helper\currentUser;
use App\Service\MenuService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Aspect]
class PermissionAspect extends AbstractAspect
{
    public array $annotations = [
        Permission::class,
    ];

    protected BaseRequest $request;

    protected UserService $service;

    protected currentUser $currentUser;

    public function __construct(
        UserService $service,
        BaseRequest $request,
        currentUser $currentUser,
    ) {
        $this->service = $service;
        $this->request = $request;
        $this->currentUser = $currentUser;
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /* @var Permission $permission */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class])) {
            $permission = $proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class];
        }
        if ($this->currentUser->isSuperAdmin() || empty($permission->code)) {
            return $proceedingJoinPoint->process();
        }
        $this->checkPermission($permission->code, $permission->where);
        return $proceedingJoinPoint->process();
    }

    /**
     * 检查权限.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function checkPermission(string $codeString, string $where): bool
    {
        $codes = $this->service->info()['codes'];

        if (preg_match_all('#{(.*?)}#U', $codeString, $matches)) {
            if (isset($matches[1])) {
                foreach ($matches[1] as $name) {
                    $codeString = str_replace('{' . $name . '}', $this->request->route($name), $codeString);
                }
            }
        }

        if ($where === 'OR') {
            foreach (explode(',', $codeString) as $code) {
                if (in_array(trim($code), $codes)) {
                    return true;
                }
            }
            throw new NoPermissionException(ErrorCode::FORBIDDEN, $this->request->getPathInfo() . ErrorCode::FORBIDDEN->getMessage());
        }

        if ($where === 'AND') {
            foreach (explode(',', $codeString) as $code) {
                $code = trim($code);
                if (! in_array($code, $codes)) {
                    $service = di()->get(MenuService::class);
                    throw new NoPermissionException(ErrorCode::FORBIDDEN, $service->findNameByCode($code) . ErrorCode::FORBIDDEN->getMessage());
                }
            }
        }

        return true;
    }
}

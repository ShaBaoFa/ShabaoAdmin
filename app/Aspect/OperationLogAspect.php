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

use App\Annotation\OperationLog;
use App\Annotation\Permission;
use App\Base\BaseRequest;
use App\Constants\ErrorCode;
use App\Events\Operation;
use App\Helper\currentUser;
use App\Helper\Ip2region;
use App\Service\MenuService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;

#[Aspect]
class OperationLogAspect extends AbstractAspect
{
    public array $annotations = [
        OperationLog::class,
    ];

    protected Ip2region $ip2region;

    protected ContainerInterface $container;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->ip2region = $this->container->get(Ip2region::class);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint): ResponseInterface
    {
        $annotation = $proceedingJoinPoint->getAnnotationMetadata()->method[OperationLog::class];
        /* @var $result ResponseInterface */
        $result = $proceedingJoinPoint->process();
        $isDownload = false;
        if (! empty($annotation->menuName) || ($annotation = $proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class])) {
            if (! empty($result->getHeader('content-description')) && ! empty($result->getHeader('content-transfer-encoding'))) {
                $isDownload = true;
            }
            $evDispatcher = $this->container->get(EventDispatcherInterface::class);
            $evDispatcher->dispatch(new Operation($this->getRequestInfo([
                'code' => ! empty($annotation->code) ? explode(',', $annotation->code)[0] : '',
                'name' => $annotation->menuName ?? '',
                'response_code' => $result->getStatusCode(),
                'response_data' => $isDownload ? '文件下载' : $result->getBody()->getContents(),
            ])));
        }
        return $result;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function getRequestInfo(array $data): array
    {
        $request = $this->container->get(BaseRequest::class);
        $loginUser = $this->container->get(currentUser::class);

        $operationLog = [
            'time' => date('Y-m-d H:i:s', $request->getServerParams()['request_time']),
            'method' => $request->getServerParams()['request_method'],
            'router' => $request->getServerParams()['path_info'],
            'protocol' => $request->getServerParams()['server_protocol'],
            'ip' => $request->ip(),
            'ip_location' => $this->ip2region->search($request->ip()),
            'service_name' => $data['name'] ?: $this->getOperationMenuName($data['code']),
            'request_data' => $request->all(),
            'response_code' => $data['response_code'],
            'response_data' => $data['response_data'],
        ];
        try {
            $operationLog['username'] = $loginUser->getUsername();
        } catch (\Exception $e) {
            $operationLog['username'] = ErrorCode::NO_LOGIN_USER->getMessage();
        }

        return $operationLog;
    }

    /**
     * 获取菜单名称.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getOperationMenuName(string $code): string
    {
        return $this->container->get(MenuService::class)->findNameByCode($code);
    }
}

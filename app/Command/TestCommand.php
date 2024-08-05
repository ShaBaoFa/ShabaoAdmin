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

namespace App\Command;

use App\Constants\FileSystemCode;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerInterface;
use Wlfpanda1012\AliyunSts\Constants\OSSAction;
use Wlfpanda1012\AliyunSts\Constants\OSSEffect;
use Wlfpanda1012\AliyunSts\StsService;

#[Command]
class TestCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('demo:command');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle(): void
    {
        // 示例
        $service = di()->get(StsService::class);
        $put = $service->generateStatement(OSSEffect::ALLOW->value, [OSSAction::PUT_OBJECT->value], ['acs:oss:*:*:wlf-upload-file/2024/02/16/*']);
        $get = $service->generateStatement(OSSEffect::ALLOW->value, [OSSAction::GET_OBJECT->value], ['acs:oss:*:*:wlf-upload-file/2024/02/16/hgignore_global.txt']);
        $policy = $service->generatePolicy([$put,$get]);
        $request = $service->generateAssumeRoleRequest($policy);
        $response = $service->assumeRole($request);
        var_dump($response->body->credentials->accessKeyId);
        var_dump($response->body->credentials->accessKeySecret);
        var_dump($response->body->credentials->securityToken);
        $filesystem = di()->get(FilesystemFactory::class)->get(FileSystemCode::OSS->value);

    }
}

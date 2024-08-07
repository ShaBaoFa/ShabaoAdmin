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

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use OSS\Core\OssException;
use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Wlfpanda1012\AliyunSts\Oss\OssRamService;

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

    /**
     * @throws NotFoundExceptionInterface
     * @throws RequestCore_Exception
     * @throws ContainerExceptionInterface
     * @throws OssException
     */
    public function handle(): void
    {
        var_dump(md5('helloworld',true));
        $encode = base64_encode(md5('helloworld',true));
        var_dump(base64_decode($encode));
        var_dump(hex2bin(md5('helloworld')));
        // 示例
        return;
        $config = di()->get(ConfigInterface::class)->get('sts');
        $config['bucket'] = 'wlf-upload-file';
        $service = new OssRamService($config);
        $credentials = $service->allowPutObject('2024/02/16/tdddw3ww.txt');
        var_dump($credentials);
        $fileConfig = di()->get(ConfigInterface::class)->get('file');
        $ossConfig = $fileConfig['storage']['oss'];
        $client = new OssClient($credentials['AccessKeyId'], $credentials['AccessKeySecret'], $ossConfig['endpoint'], false, $credentials['SecurityToken']);
        try {
            $data = $client->putObject('wlf-upload-file', '2024/02/16/tdddw3ww.txt', '123');
            //            $data = $client->getObject('wlf-upload-file', '2024/02/16/ceshice1231231shi111.txt');
        } catch (OssException $e) {
            var_dump($e->getMessage());
        }
    }
}

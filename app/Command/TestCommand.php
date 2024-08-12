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

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use OSS\Core\OssException;
use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Wlfpanda1012\AliyunSts\Constants\OSSClientCode;
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
        // gencallback
        //        var_dump($this->generateOssCallback(['hash' => "333333423"]));
        //        var_dump($this->genCallback());
        //        var_dump($this->genCallback() === $this->generateOssCallback(['hash' => "333333423"]));
        //        return;
        // OSS简单上传回
        $config = di()->get(ConfigInterface::class)->get('sts');
        $service = new OssRamService($config);
        $credentials = $service->allowPutObject('2024/02/16/tdddw3ww.txt');
        $client = new OssClient($credentials['access_key_id'], $credentials['access_key_secret'], 'https://oss-cn-hangzhou.aliyuncs.com', false, $credentials['security_token']);
        try {
            $data = $client->putObject($config['oss']['bucket'], '2024/02/16/tdddw3ww.txt', '123', $this->generateOssCallback(['hash' => '333333423']));
            print_r($data['body']);
            print_r($data['info']['http_code']);
            //            $data = $client->getObject('wlf-upload-file', '2024/02/16/ceshice1231231shi111.txt');
        } catch (OssException $e) {
            var_dump($e->getMessage());
        }
    }

    private function generateOssCallback(array $customParams = []): array
    {
        $sts = di()->get(ConfigInterface::class)->get('sts');
        $callback = $sts['oss']['callback'];
        ! json_encode($callback) ?? throw new BusinessException(ErrorCode::SERVER_ERROR);
        if (empty($customParams)) {
            return [OSSClientCode::OSS_CALLBACK->value => json_encode($callback)];
        }
        $callback[OSSClientCode::OSS_CALLBACK_BODY->value] = $this->generateOssCallbackBody($customParams);
        return [
            OSSClientCode::OSS_CALLBACK->value => json_encode($callback),
            OSSClientCode::OSS_CALLBACK_VAR->value => $this->generateOssCallbackVar($customParams),
        ];
    }

    private function generateOssCallbackBody(?array $customParams = null): string
    {
        $sts = di()->get(ConfigInterface::class)->get('sts');
        $callback = $sts['oss']['callback'];
        $baseParams = is_string($callback[OSSClientCode::OSS_CALLBACK_BODY->value]) ? explode(OSSClientCode::OSS_CALLBACK_SEPARATOR->value, $callback[OSSClientCode::OSS_CALLBACK_BODY->value]) : $callback[OSSClientCode::OSS_CALLBACK_BODY->value];
        ! is_array($baseParams) && throw new BusinessException(ErrorCode::SERVER_ERROR);

        // 遍历传入的数组，将其格式化为 'key=value' 的形式
        foreach ($customParams as $key => $value) {
            $variable = '${' . OSSClientCode::OSS_CALLBACK_CUSTOM_VAR_PREFIX->value . $key . '}';
            $baseParams[] = "{$key}={$variable}";
        }

        // 将所有参数用 & 连接成一个字符串
        return implode(OSSClientCode::OSS_CALLBACK_SEPARATOR->value, $baseParams);
    }

    private function generateOssCallbackVar($customParams): bool|string
    {
        // 设置发起回调请求的自定义参数，由Key和Value组成，Key必须以枚举指定的前缀开始。
        $var = [];
        foreach ($customParams as $key => $value) {
            $var[OSSClientCode::OSS_CALLBACK_CUSTOM_VAR_PREFIX->value . $key] = $value;
        }
        return json_encode($var);
    }

    private function genCallback(): array
    {
        $url = '{"callbackUrl":"https:\/\/zjdx-dev.cloudvhall.com:40001\/api\/v1\/ossCallback","callbackHost":"zjdx-dev.cloudvhall.com:40001","callbackBody":"filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}&hash=${x:hash}","callbackSNI":false,"callbackBodyType":"application\/x-www-form-urlencoded"}';

        // 设置发起回调请求的自定义参数，由Key和Value组成，Key必须以x:开始。
        $var =
            '{"x:hash":"333333423"}';
        return [OssClient::OSS_CALLBACK => $url,
            OssClient::OSS_CALLBACK_VAR => $var,
        ];
    }
}

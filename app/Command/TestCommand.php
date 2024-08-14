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

use App\Amqp\Producer\DelayedMessageProducer;
use App\Amqp\Producer\MessageProducer;
use App\Constants\ErrorCode;
use App\Constants\QueueMesContentTypeCode;
use App\Exception\BusinessException;
use App\Model\Message;
use App\Vo\QueueMessageVo;
use Carbon\Carbon;
use Hyperf\Amqp\Producer;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Db;
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
        parent::__construct('demo:c');
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
        //        for ($i = 0; $i < 500; ++$i) {
        //            // 随机一个过去的时间
        //            $time = rand(0, 10);
        //            $time = Carbon::now()->subDays($time)->toDateTimeString();
        //            $send_by = rand(1, 4);
        //            $receive_by = rand(5, 10);
        //            Message::insert(['send_by' => $send_by, 'receive_by' => $receive_by, 'title' => '123', 'content' => '123', 'content_type' => '1', 'created_at' => $time]);
        //        }
        // 第一步：获取步骤1的结果并将其作为子查询
        $subQuery = Db::table('messages')
            ->selectRaw('LEAST(send_by, receive_by) AS user1, GREATEST(send_by, receive_by) AS user2, MAX(created_at) AS first_message_time')
            ->where(function ($query) {
                $query->where('send_by', 2)
                    ->orWhere('receive_by', 2);
            })
            ->where('content_type', 1)
            ->groupBy(Db::raw('LEAST(send_by, receive_by), GREATEST(send_by, receive_by)'));

        // 第二步：使用子查询与原表进行 JOIN
        $messages = Db::table('messages')
            ->joinSub($subQuery, 'sub', function ($join) {
                $join->on(Db::raw('LEAST(messages.send_by, messages.receive_by)'), '=', 'sub.user1')
                    ->on(Db::raw('GREATEST(messages.send_by, messages.receive_by)'), '=', 'sub.user2')
                    ->on('messages.created_at', '=', 'sub.first_message_time');
            })
            ->select('messages.*')
            ->get();
        // 执行查询
        var_dump(count($messages));

        return;
        //        $vo = new QueueMessageVo();
        //        $vo->setTitle('123');
        //        $vo->setContent('123');
        //        $vo->setContentType(QueueMesContentTypeCode::TYPE_ANNOUNCE);
        //        var_dump($vo->toMap());
        //        return;
        //        for ($i = 0; $i < 4; ++$i) {
        //            //            $message = new MessageProducer('produceTime:' . Carbon::now()->toDateTimeString());
        //            $message = new DelayedMessageProducer($i);
        //            //            $producer = di()->get(Producer::class);
        //            //            var_dump($producer->produce($message,true));
        //        }
        // amqp
        // 1.delayed + direct
        // 发送50次 delay+direct消息
        //        for ($i = 0; $i < 1; $i++) {
        //            $message = new DelayedMessageProducer('delay+direct produceTime:' . Carbon::now()->toDateTimeString());
        //            $message->setDelayMs(5000);
        //            $producer = di()->get(Producer::class);
        //            $producer->produce($message);
        //        }
        return;
        // gencallback
        //        var_dump($this->generateOssCallback(['hash' => "333333423"]));
        //        var_dump($this->genCallback());
        //        var_dump($this->genCallback() === $this->generateOssCallback(['hash' => "333333423"]));
        //        return;
        // OSS简单上传回
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

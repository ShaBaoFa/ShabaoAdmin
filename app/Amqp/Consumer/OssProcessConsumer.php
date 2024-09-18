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

namespace App\Amqp\Consumer;

use App\Base\BaseConsumer;
use App\Service\FileSystemService;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Builder\QueueBuilder;
use Hyperf\Collection\Arr;
use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Inject;
use OSS\Core\OssException;
use OSS\Http\RequestCore_Exception;
use PhpAmqpLib\Wire\AMQPTable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Consumer(exchange: 'web-api', routingKey: 'oss.process.routing', queue: 'oss.process.queue', name: 'OssProcessConsumer', nums: 1)]
class OssProcessConsumer extends BaseConsumer
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Inject]
    protected FileSystemService $consumeService;

    #[Value('file.storage.oss')]
    private array $ossConfigValue;

    /**
     * Overwrite.
     */
    public function getQueueBuilder(): QueueBuilder
    {
        return parent::getQueueBuilder()
            ->setArguments(new AMQPTable([
                'x-dead-letter-exchange' => 'dlx_exchange',
                'x-dead-letter-routing-key' => 'dlx_routing_key',
            ]));
    }

    public function isEnable(): bool
    {
        return true;
    }

    /**
     * @throws OssException
     * @throws RequestCore_Exception
     */
    protected function process(array $data): bool
    {
        $file = $this->consumeService->find(Arr::get($data, 'id'))->toArray();
        return $this->consumeService->saveAs($file, Arr::get($data, 'config'), Arr::get($data, 'save_obj'));
    }
}

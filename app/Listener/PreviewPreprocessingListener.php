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

use App\Events\AfterUpload;
use App\Service\FileSystemService;
use App\Service\KkFileView\PreviewService;
use Hyperf\Collection\Arr;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerInterface;

#[Listener]
class PreviewPreprocessingListener implements ListenerInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            AfterUpload::class,
        ];
    }

    public function process(object $event): void
    {
        /**
         * @var AfterUpload $event
         */
        $previewService = $this->container->get(PreviewService::class);
        $fsService = $this->container->get(FileSystemService::class);
        $url = $fsService->generateSignature(Arr::get($event->fileInfo, 'url'));
        $previewService->addTask(['url' => $url]);
    }
}

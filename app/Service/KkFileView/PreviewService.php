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

namespace App\Service\KkFileView;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Collection\Arr;
use Psr\Log\LoggerInterface;

use function Hyperf\Config\config;
use function Hyperf\Support\env;
use function Hyperf\Support\make;

class PreviewService
{
    public Client $client;

    public array $config;

    private string $addTaskUri = '/addTask';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->config = config('kkfileview');
        $this->logger = $logger;
        $this->client = make(Client::class, ['config' => $this->config]);
    }

    public function addTask(array $query = []): bool
    {
        try {
            $response = $this->client->get($this->addTaskUri, ['query' => $query]);
            return $response->getBody()->getContents() == 'success';
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function onlinePreview(string $url, array $option = []): string
    {
        $baseUri = env('APP_URL') . '/' . env('APP_PREVIEW') . '/onlinePreview';
        $query = $this->handleUrl($url, $option);
        return $baseUri . '?' . http_build_query($query);
    }

    private function handleUrl(string $url, array $option): array
    {
        $query['url'] = base64_encode($url);
        if (Arr::has($option, 'watermark')) {
            $watermark = Arr::get($option, 'watermark');
            var_dump($watermark);
            $query['watermarkTxt'] = $watermark;
        }
        return $query;
    }
}

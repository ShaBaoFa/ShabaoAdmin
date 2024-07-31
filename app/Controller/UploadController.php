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

namespace App\Controller;

use App\Annotation\Auth;
use App\Base\BaseController;
use App\Request\UploadRequest;
use App\Service\FileSystemService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use League\Flysystem\FilesystemException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/upload')]
class UploadController extends BaseController
{
    #[Inject]
    protected FileSystemService $service;

    /**
     * 上传文件.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws FilesystemException
     */
    #[PostMapping('files'),Auth]
    public function uploadFiles(UploadRequest $request): ResponseInterface
    {
        if ($request->validated() && $request->file('files')->isValid()) {
            $data = $this->service->upload($request->file('files'), $request->all());
            return empty($data) ? $this->response->fail() : $this->response->success($data);
        }
        return $this->response->fail();
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     */
    #[PostMapping('images'),Auth]
    public function uploadImages(UploadRequest $request): ResponseInterface
    {
        if ($request->validated() && $request->file('images')->isValid()) {
            $data = $this->service->upload($request->file('images'), $request->all());
            return empty($data) ? $this->response->fail() : $this->response->success($data);
        }
        return $this->response->fail();
    }
}

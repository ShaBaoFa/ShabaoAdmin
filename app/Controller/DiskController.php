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
use App\Annotation\OperationLog;
use App\Annotation\Permission;
use App\Base\BaseController;
use App\Request\DiskRequest;
use App\Service\DiskService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/disks'), Auth]
class DiskController extends BaseController
{
    #[Inject]
    protected DiskService $service;

    /**
     * 列出指定文件夹下的所有文件和子文件夹.
     */
    #[GetMapping('list/{folder_id}'), Permission('disks:list')]
    public function list(int $folder_id): ResponseInterface
    {
        return $this->response->success($this->service->listContents($folder_id));
    }

    /**
     * 保存上传完成的文件信息
     * 批量操作：接受多个文件的 hash 和相关元数据.
     */
    #[PostMapping('file/save'), Permission('disks:file:save')]
    public function saveFile(DiskRequest $request): ResponseInterface
    {
        $filesData = $request->input('files'); // 传入文件数组，每个元素包含 file_hash, name, folder_id 等
        return $this->response->success($this->service->saveFiles($filesData));
    }

    /**
     * 通过文件 hash 获取下载所需的 STS token
     * 批量操作：接受多个文件 hash.
     */
    #[PostMapping('file/download-token'), Permission('disks:download')]
    public function getDownloadToken(DiskRequest $request): ResponseInterface
    {
        $fileHashes = $request->input('file_hashes'); // 传入文件 hash 数组
        return $this->response->success($this->service->getDownloadTokens($fileHashes));
    }

    /**
     * 重命名文件或文件夹
     * 单个对象操作：接受对象的 id 和新的名称.
     */
    #[PutMapping('rename/{item_id}'), Permission('disks:rename')]
    public function rename(int $item_id, DiskRequest $request): ResponseInterface
    {
        $newName = $request->input('new_name');
        return $this->response->success($this->service->renameItem($item_id, $newName));
    }

    /**
     * 删除文件或文件夹（移动到回收站）
     * 批量操作：接受多个对象 id.
     */
    #[DeleteMapping('delete'), Permission('disks:delete')]
    public function delete(DiskRequest $request): ResponseInterface
    {
        $itemIds = $request->input('item_ids'); // 传入对象 id 数组
        return $this->response->success($this->service->deleteItems($itemIds));
    }

    /**
     * 移动文件或文件夹到目标文件夹
     * 批量操作：接受多个对象 id 和目标文件夹 id.
     */
    #[PutMapping('move'), Permission('disks:move')]
    public function move(DiskRequest $request): ResponseInterface
    {
        $items = $request->input('items'); // 传入数组，每个元素包含 item_id 和 target_folder_id
        return $this->response->success($this->service->moveItems($items));
    }

    /**
     * 复制文件或文件夹到目标文件夹
     * 批量操作：接受多个对象 id 和目标文件夹 id.
     */
    #[PostMapping('copy'), Permission('disks:copy')]
    public function copy(DiskRequest $request): ResponseInterface
    {
        $items = $request->input('items'); // 传入数组，每个元素包含 item_id 和 target_folder_id
        return $this->response->success($this->service->copyItems($items));
    }

    /**
     * 生成文件或文件夹的分享链接
     * 批量操作：接受多个对象 id.
     */
    #[PostMapping('share'), Permission('disks:share'),OperationLog]
    public function share(DiskRequest $request): ResponseInterface
    {
        $items = $request->input('items'); // 传入对象 id 数组
        return $this->response->success($this->service->shareItems($items));
    }

    /**
     * 从回收站还原文件或文件夹
     * 批量操作：接受多个对象 id.
     */
    #[PutMapping('recovery'), Permission('disks:recovery')]
    public function recovery(DiskRequest $request): ResponseInterface
    {
        $itemIds = $request->input('item_ids'); // 传入文件或文件夹的 id 数组
        return $this->response->success($this->service->recovery($itemIds));
    }

    /**
     * 从回收站中永久删除文件或文件夹
     * 批量操作：接受多个对象 id.
     */
    #[DeleteMapping('realDelete'), Permission('disks:realDelete'),OperationLog]
    public function realDelete(DiskRequest $request): ResponseInterface
    {
        $itemIds = $request->input('item_ids'); // 传入文件或文件夹的 id 数组
        return $this->response->success($this->service->realDelete($itemIds));
    }
}

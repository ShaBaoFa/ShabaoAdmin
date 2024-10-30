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

namespace App\Service;

use App\Base\BaseService;
use App\Constants\CommentType;
use App\Constants\ErrorCode;
use App\Dao\CommentDao;
use App\Exception\BusinessException;
use App\Model\ExhLibObj;
use Hyperf\Collection\Arr;

class CommentService extends BaseService
{
    /**
     * @var CommentDao
     */
    public $dao;

    public function __construct(CommentDao $dao)
    {
        $this->dao = $dao;
    }

    public function save(array $data): mixed
    {
        $data = $this->handleData($data);
        return parent::save($data);
    }

    public function index(array $params): array
    {
        $params = $this->handleData($params);

        Arr::set($params, '_with', [
            'createdBy' => ['fields' => ['id', 'nickname']],
            'sentTo' => ['fields' => ['id', 'nickname']],
            'subComments' => [
                'aggregate' => [
                    'count' => '*',              // 获取评论数量
                    //                    'max' => 'star_count',           // 获取最高评分
                    //                    'avg' => 'star_count',             // 获取平均分
                ],
            ],
        ]);
        return $this->getPageList($params, false);
    }

    public function handleData(array $params): array
    {
        match ((int) Arr::get($params, 'commentable_type')) {
            CommentType::EXHIBITION_OBJECT->value => $params['commentable_type'] = ExhLibObj::class,
            default => throw new BusinessException(ErrorCode::INVALID_PARAMS)
        };
        if (! Arr::get($params, 'sent_to')) {
            $params['sent_to'] = 0;
        }
        if (! Arr::get($params, 'parent_id')) {
            $params['parent_id'] = 0;
        }
        return $params;
    }

    public function addStar(int $id): bool
    {
        $this->dao->addStar($id);
        return true;
    }
}

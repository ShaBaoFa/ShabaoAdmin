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

use function App\Helper\user;

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
        $id = parent::save($data);
        if ($id > 0 && Arr::get($data, 'sent_to') > 0) {
            $ms = di()->get(MessageService::class);
            $model = $this->find($id);
            $ms->replyTo((int) Arr::get($data, 'sent_to'), json_encode($model));
        }
        return $id;
    }

    public function index(array $params): array
    {
        $params = $this->handleData($params);

        Arr::set($params, '_with', [
            'createdBy' => ['fields' => ['id', 'nickname']],
            'sentTo' => ['fields' => ['id', 'nickname']],
            'starUsers as has_star' => [
                'conditions' => [
                    ['user_id', '=', user()->getId()],
                ],
                'aggregate' => [
                    'count' => '*',              // 获取个人是否点赞
                ],
            ],
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

    public function cancelStar(int $id): bool
    {
        $this->dao->cancelStar($id);
        return true;
    }
}

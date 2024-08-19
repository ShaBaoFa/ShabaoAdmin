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

namespace App\Base\Trait;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\Role;
use App\Model\User;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;

use function App\Helper\user;
use function Hyperf\Config\config;
use function Hyperf\Support\env;

trait ModelMacroTrait
{
    /**
     * 注册自定义方法.
     */
    private function registerExhibitionRoleDataScope(): void
    {
        // 因为展会数据出现互通
        $model = $this;
    }

    private function registerUserDataScope(): void
    {
        // 数据权限方法
        $model = $this;
        Builder::macro('userDataScope', function (?int $userid = null) use ($model) {
            if (! config('base-common.data_scope_enabled')) {
                return $this;
            }

            $userid = is_null($userid) ? user()->getId() : $userid;

            if (empty($userid)) {
                throw new BusinessException(ErrorCode::SERVER_ERROR, 'Data Scope missing user_id');
            }

            /* @var Builder $this */
            if ($userid == env('SUPER_ADMIN')) {
                return $this;
            }

            if (! in_array($model->getDataScopeField(), $model->getFillable())) {
                return $this;
            }

            $dataScope = new class($userid, $this, $model) {
                // 用户ID
                protected int $userid;

                // 查询构造器
                protected Builder $builder;

                // 数据范围用户ID列表
                protected array $userIds = [];

                // 外部模型
                protected mixed $model;

                public function __construct(int $userid, Builder $builder, mixed $model)
                {
                    $this->userid = $userid;
                    $this->builder = $builder;
                    $this->model = $model;
                }

                public function execute(): Builder
                {
                    $this->getUserDataScope();
                    return empty($this->userIds)
                        ? $this->builder
                        : $this->builder->whereIn($this->model->getDataScopeField(), array_unique($this->userIds));
                }

                protected function getUserDataScope(): void
                {
                    /**
                     * @phpstan-ignore-next-line
                     */
                    $userModel = User::find($this->userid, ['id']);
                    $roles = $userModel->roles()->get(['id', 'data_scope']);

                    foreach ($roles as $role) {
                        switch ($role->data_scope) {
                            case Role::ALL_SCOPE:
                                // 如果是所有权限，跳出所有循环
                                break 2;
                            case Role::CUSTOM_SCOPE:
                                // 自定义数据权限(暂时废弃)
                                $deptIds = $role->depts()->pluck('id')->toArray();
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    Db::table('department_user')->whereIn('department_id', $deptIds)->pluck('user_id')->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                            case Role::SELF_ORGANIZATION_SCOPE:
                                // 本组织数据权限
                                $orgIds = Db::table('organization_user')->where('user_id', $userModel->id)->pluck('organization_id')->toArray();
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    Db::table('organization_user')->whereIn('organization_id', $orgIds)->pluck('user_id')->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                            case Role::ORGANIZATION_AUDIT_SCOPE:
                                // 需要本组织审核数据
                                // 判断是否是审核表
                                if (! $this->model->isAuditAble()) {
                                    break;
                                }
                                $orgIds = Db::table('organization_user')->where('user_id', $userModel->id)->pluck('organization_id')->toArray();
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    $this->model->whereIn('approving_id', $orgIds)->pluck($this->model->getDataScopeField())->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                                break;
                            case Role::SELF_SCOPE:
                                $this->userIds[] = $this->userid;
                                break;
                            default:
                                break;
                        }
                    }
                }
            };

            return $dataScope->execute();
        });
    }

    /**
     * Description:注册常用自定义方法
     * User:mike.
     */
    private function registerBase(): void
    {
        // 添加andFilterWhere()方法
        Builder::macro('andFilterWhere', function ($key, $operator, $value = null) {
            if ($value === '' || $value === '%%' || $value === '%') {
                return $this;
            }
            if ($operator === '' || $operator === '%%' || $operator === '%') {
                return $this;
            }
            if ($value === null) {
                return $this->where($key, $operator);
            }
            return $this->where($key, $operator, $value);
        });

        // 添加orFilterWhere()方法
        Builder::macro('orFilterWhere', function ($key, $operator, $value = null) {
            if ($value === '' || $value === '%%' || $value === '%') {
                return $this;
            }
            if ($operator === '' || $operator === '%%' || $operator === '%') {
                return $this;
            }
            if ($value === null) {
                return $this->orWhere($key, $operator);
            }
            return $this->orWhere($key, $operator, $value);
        });
    }
}

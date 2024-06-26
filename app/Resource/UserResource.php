<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Resource;

use Carbon\Carbon;
use Hyperf\Resource\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class UserResource.
 * @property int $id
 *                   * @property string $username
 *                   * @property int $status
 *                   * @property string $login_ip
 *                   * @property Carbon $login_time
 *                   * @property string $remark
 *                   * @property Carbon $created_at
 *                   * @property Carbon $updated_at
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    #[ArrayShape([
        'id' => 'integer',
        'username' => 'string',
        'status' => 'integer',
        'login_ip' => 'string',
        'login_time' => 'datetime',
        'remark' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'status' => $this->status,
            'login_ip' => $this->login_ip,
            'login_time' => $this->login_time?->toDateTimeString(),
            'remark' => $this->remark,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

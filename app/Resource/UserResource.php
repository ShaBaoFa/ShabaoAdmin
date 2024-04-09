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
 * @property string $account
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    #[ArrayShape([
        'id' => 'int',
        'account' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'account' => $this->account,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}

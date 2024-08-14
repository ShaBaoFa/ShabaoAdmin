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

namespace App\Model;

use App\Base\BaseModel;

/**
 * @property int $exhibition_id 展会主键
 * @property int $hall_id 展馆主键
 */
class ExhibitionHall extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exhibition_hall';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['exhibition_id', 'hall_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['exhibition_id' => 'integer', 'hall_id' => 'integer'];
}

<?php

declare(strict_types=1);

namespace App\Model;

use App\Base\BaseModel;

/**
 * @property int $user_id 
 * @property int $post_id 
 */
class PostUser extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'post_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'post_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_id' => 'integer', 'post_id' => 'integer'];
}

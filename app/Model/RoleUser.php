<?php

declare(strict_types=1);

namespace App\Model;

use App\Base\BaseModel;

/**
 * @property int $user_id 
 * @property int $role_id 
 */
class RoleUser extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'role_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_id' => 'integer', 'role_id' => 'integer'];
}

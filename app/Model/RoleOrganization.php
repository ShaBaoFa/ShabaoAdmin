<?php

declare(strict_types=1);

namespace App\Model;

use App\Base\BaseModel;

/**
 * @property int $organization_id 
 * @property int $role_id 
 */
class RoleOrganization extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role_organization';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['organization_id', 'role_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['organization_id' => 'integer', 'role_id' => 'integer'];
}

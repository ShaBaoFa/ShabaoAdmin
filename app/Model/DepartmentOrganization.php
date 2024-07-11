<?php

declare(strict_types=1);

namespace App\Model;

use App\Base\BaseModel;

/**
 * @property int $department_id 
 * @property int $organization_id 
 */
class DepartmentOrganization extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'department_organization';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['department_id', 'organization_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['department_id' => 'integer', 'organization_id' => 'integer'];
}

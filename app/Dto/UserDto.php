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

namespace App\Dto;

use App\Annotation\ExcelData;
use App\Annotation\ExcelProperty;
use App\Interfaces\ModelExcelInterface;

/**
 * 用户DTO.
 */
#[ExcelData]
class UserDto implements ModelExcelInterface
{
    #[ExcelProperty(value: '用户名', index: 0)]
    public string $username;

    #[ExcelProperty(value: '密码', index: 3)]
    public string $password;

    #[ExcelProperty(value: '昵称', index: 1)]
    public string $nickname;

    #[ExcelProperty(value: '手机', index: 2)]
    public string $phone;

    #[ExcelProperty(value: '状态', index: 4, dictData: [1 => '正常', 2 => '禁用'])]
    public string $status;
}

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
use function Hyperf\Support\env;

return [
    // 是否启用验证
    'enable' => (bool) env('CAPTCHA_ENABLE', true),
    // 验证码位数
    'length' => 5,
    // 验证码字符集合
    'code_set' => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
    // 验证码过期时间
    'expired' => 600,
    // 是否使用算术验证码
    'math' => true,
    // 验证码字符大小
    'font_size' => 25,
    // 是否使用混淆曲线
    'use_curve' => true,
    // 是否添加杂点
    'use_noise' => true,
    // 背景颜色
    'background_color' => [243, 251, 254],
];

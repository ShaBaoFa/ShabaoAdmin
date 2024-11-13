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
return [
    'default' => [
        // 字符集
        'charset' => '123456789AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvWwXxYyZz',
        // 生成位数
        'length' => 4,
        // 混淆曲线
        'confusionCurve' => false,
        // 随机噪点
        'randomNoise' => false,
        // 指定字体
        'useFont' => null,
        // 字体大小
        'fontSize' => 25,
        // 字体颜色
        'fontColor' => null,
        // 背景颜色
        'backgroundColor' => [255, 255, 255],
        // 图片宽度
        'captchaWidth' => null,
        // 图片高度
        'captchaHeight' => null,
        // 额外字体
        'fonts' => [],
    ],
];

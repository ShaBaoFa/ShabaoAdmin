# 介绍
Hyperf 骨架包
[https://gitee.com/limingxinleo/biz-skeleton.git](https://gitee.com/limingxinleo/biz-skeleton.git)

# 安装 composer 包
```shell
# swagger 文档生成
composer require "hyperf/swagger:3.1.*" -W
```
```shell
# validation laravel 验证器
composer require "hyperf/validation:3.1.*" -W
```

# 发布配置
```shell
# [hyperf/swagger] publishes [config] successfully.
php bin/hyperf.php vendor:publish hyperf/swagger
# [hyperf/validation] publishes [zh_CN] successfully.
# [hyperf/validation] publishes [en] successfully.
php bin/hyperf.php vendor:publish hyperf/validation
```
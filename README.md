# 🚀介绍
Hyperf 骨架包
[https://gitee.com/limingxinleo/biz-skeleton.git](https://gitee.com/limingxinleo/biz-skeleton.git)

# 🔧安装 composer 包
```shell
# swagger 文档生成
composer require "hyperf/swagger:3.1.*" -W
# validation laravel 验证器
composer require "hyperf/validation:3.1.*" -W
# hyperf-utils 工具包
composer require "limingxinleo/hyperf-utils" -W
# hyperf resource
composer require "hyperf/resource:3.1.*" -W
# ip2region
composer require "zoujingli/ip2region" -W
# Excel （需要 ext-xml）
composer require "phpoffice/phpspreadsheet:^1.24" -W
# 更换jwt-auth（基于 lcobucci/jwt 的 优化版）
composer require "xmo/jwt-auth" -W

```

# 🤖发布配置
```shell
# [hyperf/swagger] publishes [config] successfully.
php bin/hyperf.php vendor:publish hyperf/swagger
# [hyperf/validation] publishes [zh_CN] successfully.
# [hyperf/validation] publishes [en] successfully.
php bin/hyperf.php vendor:publish hyperf/validation
php bin/hyperf.php vendor:publish hyperf/translation
# auth 权限验证
php bin/hyperf.php ndor:publish 96qbhy/hyperf-auth
```

# 生成&发布 `.env` 文件
```shell
cp .env.example .env
php bin/hyperf.php gen:auth-env
```
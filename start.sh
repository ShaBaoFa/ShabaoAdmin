docker pull registry.cn-hangzhou.aliyuncs.com/hzkjyzz/zly-api:latest
docker stack deploy -c /var/www/zly-api/deploy.yml --with-registry-auth zly-api
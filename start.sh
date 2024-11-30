docker pull registry.cn-hangzhou.aliyuncs.com/hzkjyzz/zly-api:latest
docker service rm zly-api_zly-api
docker config rm zly-api_v1
docker stack deploy -c /var/www/zly-api/deploy.yml --with-registry-auth zly-api
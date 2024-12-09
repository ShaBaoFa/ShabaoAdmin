docker pull registry.cn-shanghai.aliyuncs.com/your_namespace/your_project:latest
docker stack deploy -c /opt/www/your_project/deploy.yml --with-registry-auth your_project
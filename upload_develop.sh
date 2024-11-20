#!/bin/sh

scp .env.dev root@114.55.114.132:/var/www/zly-api/.env
scp deploy.dev.yml root@114.55.114.132:/var/www/zly-api/deploy.yml
scp start.sh root@114.55.114.132:/var/www/zly-api/start.sh

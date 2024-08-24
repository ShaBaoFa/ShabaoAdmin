FROM rabbitmq:management

LABEL authors="weilingfeng"
# 更新包列表并安装curl
RUN apt-get update && apt-get install -y curl
RUN /bin/sh -c set eux;
# 下载插件并将其移动到RabbitMQ的默认插件目录
# 启用插件
RUN curl -L -o /plugins/rabbitmq_delayed_message_exchange-3.13.0.ez \
    https://github.com/rabbitmq/rabbitmq-delayed-message-exchange/releases/download/v3.13.0/rabbitmq_delayed_message_exchange-3.13.0.ez && \
    rabbitmq-plugins enable --offline rabbitmq_delayed_message_exchange


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
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 声明一个持久化的队列
$channel->queue_declare('task_queue', false, true, false, false);

// 开启发布者确认模式
$channel->confirm_select();

$channel->set_ack_handler(function (AMQPMessage $message) {
    // 处理消息已被RabbitMQ确认的逻辑
    echo 'Message acked: ' . $message->body . PHP_EOL;
});

$channel->set_nack_handler(function (AMQPMessage $message) {
    // 处理消息未被RabbitMQ确认的逻辑
    echo 'Message nacked: ' . $message->body . PHP_EOL;
});

$data = 'Hello, World!';
$msg = new AMQPMessage($data, [
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
]);

$channel->basic_publish($msg, '', 'task_queue');

// 等待确认
try {
    $channel->wait_for_pending_acks_returns();
    echo 'Message was successfully published and confirmed.' . PHP_EOL;
} catch (AMQPTimeoutException $e) {
    echo 'Message was not confirmed!' . PHP_EOL;
}

$channel->close();
$connection->close();

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

namespace App\Vo;

use App\Constants\QueueMesContentTypeCode;
use Hyperf\Collection\Arr;

class QueueMessageVo
{
    /**
     * 消息标题.
     */
    protected string $title = '';

    /**
     * 消息类型.
     */
    protected QueueMesContentTypeCode $contentType;

    /**
     * 消息内容.
     */
    protected string $content = '';

    /**
     * 发送人.
     */
    protected int $sendBy = 0;

    /**
     * 备注.
     */
    protected string $remark = '';

    /**
     * 是否需要确认.
     */
    protected bool $isConfirm = false;

    /**
     * 队列超时时间.
     */
    protected int $timeout = 5;

    /**
     * 队列延迟生产时间秒.
     */
    protected int $delayTime = 0;

    /**
     * 额外配置.
     */
    protected array $options = [];

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function setTitle(string $title): QueueMessageVo
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContentType(): QueueMesContentTypeCode
    {
        return $this->contentType;
    }

    /**
     * @return $this
     */
    public function setContentType(QueueMesContentTypeCode $contentType): QueueMessageVo
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return $this
     */
    public function setContent(string $content): QueueMessageVo
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getSendBy(): int
    {
        return $this->sendBy;
    }

    /**
     * @param string $sendBy
     */
    public function setSendBy(int $sendBy): QueueMessageVo
    {
        $this->sendBy = $sendBy;
        return $this;
    }

    public function getRemark(): string
    {
        return $this->remark;
    }

    public function setRemark(string $remark): QueueMessageVo
    {
        $this->remark = $remark;
        return $this;
    }

    public function getIsConfirm(): bool
    {
        return $this->isConfirm;
    }

    public function setIsConfirm(bool $isConfirm): QueueMessageVo
    {
        $this->isConfirm = $isConfirm;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): QueueMessageVo
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getDelayTime(): int
    {
        return $this->delayTime;
    }

    public function setDelayTime(int $delayTime): QueueMessageVo
    {
        $this->delayTime = $delayTime;
        return $this;
    }

    public function toMap(): array
    {
        return [
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'content_type' => $this->getContentType()->value,
            'send_by' => $this->getSendBy(),
            'remark' => $this->getRemark(),
            'is_confirm' => $this->getIsConfirm(),
            'timeout' => $this->getTimeout(),
            'delay_time' => $this->getDelayTime(),
            'options' => $this->getOptions(),
        ];
    }

    public function fromMap(array $map): QueueMessageVo
    {
        $this->setTitle($map['title'] ?? $this->getTitle());
        $this->setContent($map['content'] ?? $this->getContent());

        if (Arr::has($map, 'content_type') && QueueMesContentTypeCode::tryFrom($map['content_type']) !== null) {
            $this->setContentType(QueueMesContentTypeCode::from($map['content_type']));
        }

        $this->setSendBy($map['send_by'] ?? $this->getSendBy());
        $this->setRemark($map['remark'] ?? $this->getRemark());
        $this->setIsConfirm($map['is_confirm'] ?? $this->getIsConfirm());
        $this->setTimeout($map['timeout'] ?? $this->getTimeout());
        $this->setDelayTime($map['delay_time'] ?? $this->getDelayTime());
        $this->setOptions($map['options'] ?? $this->getOptions());

        return $this;
    }
}

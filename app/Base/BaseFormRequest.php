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

namespace App\Base;

use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Validation\Request\FormRequest;

class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return $this->merge(__FUNCTION__);
    }

    public function attributes(): array
    {
        return $this->merge(__FUNCTION__);
    }

    public function rules(): array
    {
        return $this->merge(__FUNCTION__);
    }

    public function merge(string $function): array
    {
        $commonFunction = 'common' . ucfirst($function);
        $actionFunction = $this->getMethodAction() . ucfirst($function);
        return array_merge(
            method_exists($this, $commonFunction) ? $this->{$commonFunction}() : [],
            method_exists($this, $actionFunction) ? $this->{$actionFunction}() : []
        );
    }

    protected function getMethodAction(): ?string
    {
        /**
         * @var Dispatched $dispatch
         */
        $dispatch = $this->getAttribute(Dispatched::class);
        $callback = $dispatch?->handler?->callback;
        if (is_array($callback) && count($callback) == 2) {
            return $callback[1];
        }
        if (is_string($callback)) {
            if (str_contains($callback, '::')) {
                return explode('::', $callback)[1] ?? null;
            }
            if (str_contains($callback, '@')) {
                return explode('@', $callback)[1] ?? null;
            }
        }
        return null;
    }
}

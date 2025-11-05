<?php

namespace App\Telegram\DTOs;

class CallbackData
{
    public function __construct(
        public readonly ?string $confession = null,
        public readonly ?string $action = null,
        public readonly ?int $actionId = null,
        public readonly ?int $confessionId = null,
        public readonly ?string $method = null
    ) {}

    public static function parse(string $data): self
    {
        // Format: confession:confession_id:action:action_id@method
        $method = null;
        $callbackPart = $data;
        if (str_contains($data, '@')) {
            [$callbackPart, $method] = explode('@', $data, 2);
        }

        $parts = explode(':', $callbackPart);

        \Log::info('CallbackData parse',$parts);

        return new self(
            confession: $parts[0] ?? null,
            action: $parts[2] ?? null,
            actionId: isset($parts[3]) ? (int)$parts[3] : null,
            confessionId: isset($parts[1]) ? (int)$parts[1] : null,
            method: $method
        );
    }

    public function build(): string
    {
        $parts = array_filter([
            $this->confession,
            $this->confessionId,
            $this->action,
            $this->actionId
        ], fn($value) => $value !== null);

        $result = implode(':', $parts);

        if ($this->method !== null) {
            $result .= '@' . $this->method;
        }

        return $result;
    }
}

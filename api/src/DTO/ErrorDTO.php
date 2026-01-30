<?php

namespace App\DTO;

class ErrorDTO implements \JsonSerializable
{
    public function __construct(
        public int $code,
        public string $description,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'description' => $this->description,
        ];
    }
}

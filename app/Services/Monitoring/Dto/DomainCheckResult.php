<?php

namespace App\Services\Monitoring\Dto;

final readonly class DomainCheckResult
{
    public function __construct(
        public bool $isUp,
        public ?int $httpCode,
        public ?int $responseTimeMs,
        public ?string $errorMessage,
    ) {
    }
}

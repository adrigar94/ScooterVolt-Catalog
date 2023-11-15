<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Auth;

interface JwtDecoder
{
    public function decodedJwtToken(): array;

    public function isExpired(): bool;

    public function roles(): array;

    public function username(): string;

    public function id(): string;
}

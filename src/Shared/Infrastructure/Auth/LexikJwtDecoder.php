<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Infrastructure\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use ScooterVolt\CatalogService\Shared\Domain\Auth\JwtDecoder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LexikJwtDecoder implements JwtDecoder
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorageInterface,
        private readonly JWTTokenManagerInterface $jwtManager
    ) {
    }

    public function decodedJwtToken(): array
    {
        return $this->jwtManager->decode($this->tokenStorageInterface->getToken());
    }

    public function isExpired(): bool
    {
        return ($this->decodedJwtToken()['exp'] ?? 0) < time();
    }

    public function roles(): array
    {
        return $this->decodedJwtToken()['ROLES'] ?? [];
    }

    public function username(): string
    {
        return $this->decodedJwtToken()['username'] ?? '';
    }

    public function id(): string
    {
        return $this->decodedJwtToken()['id'] ?? '';
    }
}

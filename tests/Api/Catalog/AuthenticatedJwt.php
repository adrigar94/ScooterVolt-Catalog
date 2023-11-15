<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use Adrigar94\ValueObjectCraft\Domain\Uuid\UuidValueObject;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait AuthenticatedJwt
{
    public function getToken(KernelBrowser $client, string $username, array $roles, string $userId = null): string
    {
        $encoder = $client->getContainer()->get(JWTEncoderInterface::class);
        $payload = [
            'username' => $username,
            'ROLES' => $roles,
            'id' => $userId ?? UuidValueObject::random(),
        ];

        return $encoder->encode($payload);
    }

    public function setAuthToken(KernelBrowser &$client, string $username, array $roles, string $userId = null): void
    {
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $this->getToken($client, $username, $roles, $userId)));
    }
}

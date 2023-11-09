<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Api\Catalog;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait AuthenticatedJwt
{
    public function getToken(KernelBrowser $client, string $username, array $roles): string
    {
        $encoder = $client->getContainer()->get(JWTEncoderInterface::class);
        $payload = [
            'username' => $username,
            'ROLES' => $roles,
        ];
        return $encoder->encode($payload);
    }

    public function setAuthToken(KernelBrowser &$client, string $username, array $roles): void
    {
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $this->getToken($client, $username, $roles)));
    }
}

<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Infrastructure\Persistence\MongoDB;

use MongoDB\Client;

class MongoDBConnection
{
    private readonly Client $client;


    public function __construct(string $uri, private readonly string $databaseName)
    {
        $this->client = new Client($uri);
    }

    public function getClient(): Client
    {
        return $this->client;
    }


    public function getDatabase(): \MongoDB\Database
    {
        return $this->client->selectDatabase($this->databaseName);
    }
}

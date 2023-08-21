<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Infrastructure\Persistence\MongoDB;

use MongoDB\Client;

class MongoDBConnection
{
    private Client $client;
    private string $databaseName;


    public function __construct(string $uri, string $databaseName)
    {
        $this->client = new Client($uri);
        $this->databaseName = $databaseName;
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

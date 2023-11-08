<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Infrastructure\Bus\Event;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\DomainEvent;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\EventBus;

class RabbitMqEventBus implements EventBus
{
    private readonly AMQPStreamConnection $connection;

    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password,
        private readonly string $exchange,
        private readonly ?string $exchangeType = 'topic'
    ) {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
    }

    public function publish(DomainEvent ...$events): void
    {
        $channel = $this->connection->channel();

        $channel->exchange_declare($this->exchange, $this->exchangeType, false, true, false);

        foreach ($events as $event) {
            $message = new AMQPMessage(
                json_encode($event->toPrimitives(), JSON_THROW_ON_ERROR),
                [
                    'message_id' => $event->eventId(),
                    'timestamp' => $event->occurredOn()->setTimezone(new \DateTimeZone("UTC"))->getTimestamp(),
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                    'content_type' => 'application/json',
                    'content_encoding' => 'utf-8',
                ]
            );

            $channel->basic_publish($message, $this->exchange, $event->eventName());
        }

        $channel->close();
        $this->connection->close();
    }


    public function consume(string $queue, string $eventName, \Closure $callbackEvent, int $maxEventReads = 10): void
    {
        $channel = $this->connection->channel();
        $channel->exchange_declare($this->exchange, $this->exchangeType, false, true, false);

        $channel->queue_declare($queue, false, true, false, false);
        $channel->queue_bind($queue, $this->exchange, $eventName);

        $channel->basic_consume($queue, '', false, true, false, false, $callbackEvent);

        $i = 0;
        while ($i < $maxEventReads && $channel->is_open()) {
            $channel->wait();
            $i++;
        }
        $channel->close();
        $this->connection->close();
    }
}
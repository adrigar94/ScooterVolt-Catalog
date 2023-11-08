<?php

namespace ScooterVolt\CatalogService\Command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'event-domain:scooter:read-all',
    description: 'Read scooter.* events',
)]
class EventDomainScooterReadAllCommand extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $MAX_READS = 10;
        $io = new SymfonyStyle($input, $output);

        $connection = new AMQPStreamConnection("127.0.0.1", 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->exchange_declare('CatalogService', 'topic', false, true, false);

        $channel->queue_declare('Scooter_ALL', false, true, false, false);
        $channel->queue_bind('Scooter_ALL', 'CatalogService', 'scooter.*');

        $channel->basic_consume('Scooter_ALL', '', false, true, false, false, function ($message) use ($io) {
            $io->info([$message->delivery_info['routing_key'], $message->body]);
        });

        $i = 0;
        while ($i < $MAX_READS && $channel->is_open()) {
            $channel->wait();
            $i++;
        }
        $channel->close();
        $connection->close();


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
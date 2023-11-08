<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\Exceptions;

class ScooterSoldChangeStatusException extends ScooterChangeStatusException
{
    public function __construct(string $targetState)
    {
        parent::__construct('sold', $targetState, 'It cannot be changed from Sold status, this is final.');
    }
}

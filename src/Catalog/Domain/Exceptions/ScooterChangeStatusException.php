<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\Exceptions;

class ScooterChangeStatusException extends \DomainException
{
    public function __construct(string $originalState, string $targetState, string $errorMessage)
    {
        $message = "Failed to transition from state '{$originalState}' to state '{$targetState}': {$errorMessage}";
        parent::__construct($message);
    }
}

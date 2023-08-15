<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\ValueObject;

class UserContactInfo implements ValueObject
{
    public function __construct(
        private string $name,
        private string $phone,
        private string $email
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function isSame(ValueObject $object): bool
    {
        if (!$object instanceof UserContactInfo) {
            return false;
        }
        return $this->name === $object->getName() && $this->phone === $object->getPhone() && $this->email === $object->getEmail();
    }

    static public function fromNative($native)
    {
        return new static(
            $native['name'],
            $native['phone'],
            $native['email']
        );
    }

    public function toNative(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email
        ];
    }

    public function __toString(): string
    {
        return $this->name . ' - ' . $this->phone . ' - ' . $this->email;
    }

    function jsonSerialize(): array
    {
        return $this->toNative();
    }
}

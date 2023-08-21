<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdStatus;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserContactInfo;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserId;

abstract class Ad implements \JsonSerializable
{

    public function __construct(
        private AdId $id,
        private AdUrl $url,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
        private AdStatus $status,
        private UserId $user_id,
        private UserContactInfo $contactInfo
    ) {
    }

    public function getId(): AdId
    {
        return $this->id;
    }

    public function getUrl(): AdUrl
    {
        return $this->url;
    }

    public function setUrl(AdUrl $url): void
    {
        $this->url = $url;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getUserId(): UserId
    {
        return $this->user_id;
    }

    public function getStatus(): AdStatus
    {
        return $this->status;
    }

    public function getUserContactInfo(): UserContactInfo
    {
        return $this->contactInfo;
    }

    public function setUserContactInfol(UserContactInfo $contactInfo): void
    {
        $this->contactInfo = $contactInfo;
    }

    abstract protected function toDraftValidations(): void;

    public function toDraft(): void
    {
        $this->toDraftValidations();
        $this->status = new AdStatus(AdStatus::DRAFT);
        $this->afterDraft();
    }

    protected function afterDraft(): void
    {
    }

    abstract protected function toPublishValidations(): void;

    public function toPublish(): void
    {
        $this->toPublishValidations();
        $this->status = new AdStatus(AdStatus::PUBLISHED);
        $this->afterPublish();
    }

    protected function afterPublish(): void
    {
    }

    abstract protected function toSoldValidations(): void;

    public function toSold(): void
    {
        $this->toSoldValidations();
        $this->status = new AdStatus(AdStatus::SOLD);
        $this->afterSold();
    }

    protected function afterSold(): void
    {
    }

    public function getContactInfo(): UserContactInfo
    {
        return $this->contactInfo;
    }

    public function setContactInfo(UserContactInfo $contactInfo): void
    {
        $this->contactInfo = $contactInfo;
    }
}

<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain;

final readonly class ScooterDTO
{
    /**
     * @param array<string, string>      $user_contact_info
     *                                                      [coords: [name: string, phone: string, email: string]
     * @param array<string, string>|null $price
     *                                                      [price: float, currency: string]
     * @param array<string, string>|null $location
     *                                                      [coords: [latitude: float, longitude: float], location: [locality: string, country: string, region: ?string, city: ?string, postalCode: ?string]]
     * @param array<string, string>|null $gallery
     *                                                      [id: ?string, url: string, alt: string]
     */
    public function __construct(
        public ?string $id,
        public string $url,
        public string $created_at,
        public string $updated_at,
        public string $status,
        public string $user_id,
        public array $user_contact_info,
        public ?string $brand = null,
        public ?string $model = null,
        public ?array $price = null,
        public ?array $location = null,
        public ?array $gallery = null,
        public ?int $year = null,
        public ?string $condition = null,
        public ?int $travel_range = null,
        public ?int $max_speed = null,
        public ?int $power = null,
        public ?string $description = null
    ) {
    }
}

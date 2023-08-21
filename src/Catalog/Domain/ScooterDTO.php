<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain;

final class ScooterDTO
{

    /**
     * @param array<string, string> $contactInfo
     *  [coords: [name: string, phone: string, email: string]
     * @param array<string, string>|null $price
     * [price: float, currency: string]
     * @param array<string, string>|null $location
     * [coords: [latitude: float, longitude: float], location: [locality: string, country: string, region: ?string, city: ?string, postalCode: ?string]]
     * @param array<string, string>|null $gallery
     * [id: ?string, url: string, alt: string]
     */
    public function __construct(
        public readonly ?string $id,
        public readonly string $url,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly string $status,
        public readonly string $user_id,
        public readonly array  $user_contact_info,
        public readonly ?string $brand = null,
        public readonly ?string $model = null,
        public readonly ?array  $price = null,
        public readonly ?array  $location = null,
        public readonly ?array  $gallery = null,
        public readonly ?int    $year = null,
        public readonly ?string $condition = null,
        public readonly ?int    $travel_range = null,
        public readonly ?int    $max_speed = null,
        public readonly ?int    $power = null
    ) {
    }
}

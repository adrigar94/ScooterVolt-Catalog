<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Search;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Filter;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\FilterOperator;

class ScooterSearchService
{
    public function __construct(
        private ScooterRepository $repository
    ) {
    }

    /**
     * @return Scooter[]
     */
    public function __invoke(Criteria $criteria): array
    {
        $filters = $criteria->filters();
        $status_is_defined = false;

        foreach ($filters as $filter) {
            $field = $filter->field();

            if ($field === 'status') {
                $status_is_defined = true;
                //TODO only is posible search self drafts or sold, except admin
            }
        }

        if (!$status_is_defined) {
            $filter_publised = new Filter('status', FilterOperator::EQUAL(), 'published');
            $filters[] = $filter_publised;
            $criteria = new Criteria($filters, $criteria->order(), $criteria->offset(), $criteria->limit());
        }
        return $this->repository->search($criteria);
    }
}
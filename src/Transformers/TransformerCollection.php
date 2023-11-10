<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Collection;

class TransformerCollection
{
    private ?Collection $transformerItems = null;

    public function __construct(
        protected TransformerFactory $transformerFactory,
        protected Collection $collection,
    ) {
    }

    /**
     * Get the transformed data for all the items.
     */
    public function getData(): array
    {
        $data = [];

        foreach ($this->getTransformerItems() as $item) {
            $data[] = $item->getData();
        }

        return $data;
    }

    /**
     * Collect all the item includes.
     *
     * @param array $relationships   the relationships that we want to include
     * @param array $alreadyIncluded the already included items
     * @param array $resourceData    resource data containing relationships (to comply with TransformerItem getIncluded)
     * @return array
     */
    public function getIncluded(array $relationships = [], array $alreadyIncluded = [], array $resourceData = []): array
    {
        $included = [];

        foreach ($this->getTransformerItems() as $item) {
            $included = array_merge(
                $included,
                $item->getIncluded($relationships, array_merge($included, $alreadyIncluded), $item->getData())
            );
        }

        return $included;
    }

    /**
     * Get a collection of TransformerItems created from the resource passed at construction.
     */
    protected function getTransformerItems(): Collection
    {
        if (isset($this->transformerItems)) {
            return $this->transformerItems;
        }

        $this->transformerItems = new Collection();

        foreach ($this->collection as $resource) {
            $this->transformerItems->push(new TransformerItem($this->transformerFactory, $resource));
        }

        return $this->transformerItems;
    }
}

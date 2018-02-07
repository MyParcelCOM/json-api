<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Collection;

class TransformerCollection
{
    protected $collection;
    protected $transformerFactory;
    private $transformerItems;

    /**
     * @param TransformerFactory $transformerFactory
     * @param Collection         $collection
     */
    public function __construct(TransformerFactory $transformerFactory, Collection $collection)
    {
        $this->collection = $collection;
        $this->transformerFactory = $transformerFactory;
    }

    /**
     * Get the transformed data for all the items.
     *
     * @return array
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
     * @return array
     */
    public function getIncluded(array $relationships = [], array $alreadyIncluded = []): array
    {
        $included = [];

        foreach ($this->getTransformerItems() as $item) {
            $included = array_merge($included, $item->getIncluded($relationships, $alreadyIncluded));
        }

        return $included;
    }

    /**
     * Get a collection of TransformerItems created from the resource passed at construction.
     *
     * @return Collection
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

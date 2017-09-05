<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

use Illuminate\Support\Collection;

class TransformerCollection
{
    protected $collection;
    protected $transformerFactory;
    protected $transformer;

    /**
     * @param TransformerFactory $transformerFactory
     * @param Collection         $collection
     */
    public function __construct(TransformerFactory $transformerFactory, Collection $collection)
    {
        $this->collection = $collection;
        $this->transformerFactory = $transformerFactory;
        if (isset($collection[0])) {
            $this->transformer = $transformerFactory->createFromModel($collection[0]);
        }
    }

    /**
     * Get the transformed data for all the items.
     *
     * @return array
     */
    public function getData(): array
    {
        $data = [];

        foreach ($this->collection as $resource) {
            $data[] = (new TransformerItem($this->transformerFactory, $resource))->getData();
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
        $data = [];

        foreach ($this->collection as $resource) {
            $data = array_merge($data, (new TransformerItem($this->transformerFactory, $resource))->getIncluded($relationships, $alreadyIncluded));
        }

        return $data;
    }
}

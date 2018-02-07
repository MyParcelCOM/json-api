<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Collection;

class TransformerItem
{
    protected $resource;

    /** @var AbstractTransformer */
    protected $transformer;

    /** @var TransformerFactory */
    protected $transformerFactory;

    /**
     * @param TransformerFactory $transformerFactory
     * @param mixed              $resource
     */
    public function __construct(TransformerFactory $transformerFactory, $resource)
    {
        $this->resource = $resource;
        $this->transformerFactory = $transformerFactory;
        $this->transformer = $transformerFactory->createFromModel($resource);
    }

    /**
     * Get the transformed data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->transformer->transform($this->resource);
    }

    /**
     * Get the items to add to the include list.
     *
     * @param array $relationships   the relationships that we want to include
     * @param array $alreadyIncluded the already included items
     * @return array
     */
    public function getIncluded(array $relationships = [], array $alreadyIncluded = []): array
    {
        $included = [];
        $filtered = $this->getFilteredIncludes($relationships, $alreadyIncluded);

        foreach ($filtered as $relationship => $resourceCallback) {
            $resource = $resourceCallback();

            if ($resource instanceof Collection) {
                $data = $this->transformerFactory->createTransformerCollection($resource)->getData();
            } else {
                $data = [$this->transformerFactory->createTransformerItem($resource)->getData()];
            }

            $included = array_merge($included, $data);
        }

        return $included;
    }

    /**
     * Filter which includes are still required to include.
     *
     * @param array $keyFilter   keys we need to include
     * @param array $valueFilter values already included
     * @return array
     */
    protected function getFilteredIncludes(array $keyFilter = [], array $valueFilter = []): array
    {
        $filtered = [];
        $valueFilter = array_map(function ($e) {

            return isset($e['type']) && isset($e['id']) ? $e['type'] . '-' . $e['id'] : '';
        }, $valueFilter);
        $included = $this->transformer->getIncluded($this->resource);

        foreach ($included as $key => $includes) {
            if (!in_array($key, $keyFilter)) {
                continue;
            }

            /**
             * Get the relationship data with key from the transformer
             */
            $relationships = $this->transformer->getRelationships($this->resource)[$key]['data'];

            /**
             * If $relationships is a single item instead of an array of items, we put it in an array.
             */
            if (isset($relationships['type'])) {
                $relationships = [$relationships];
            }

            /**
             * We check for all the items if they are already included if not we add them to the list
             */
            foreach ($relationships as $relation) {
                if (!in_array($relation['type'] . '-' . $relation['id'], $valueFilter)) {
                    $filtered[$key] = $includes;
                    break;
                }
            }
        }

        return $filtered;
    }
}

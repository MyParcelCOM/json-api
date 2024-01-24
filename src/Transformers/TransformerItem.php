<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TransformerItem
{
    protected TransformerInterface $transformer;

    public function __construct(
        protected TransformerFactory $transformerFactory,
        protected mixed $resource,
    ) {
        $this->transformer = $transformerFactory->createFromModel($resource);
    }

    /**
     * Get the transformed data.
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
     * @param array $resourceData    resource data containing relationships (to avoid expensive lookups)
     * @return array
     */
    public function getIncluded(array $relationships = [], array $alreadyIncluded = [], array $resourceData = []): array
    {
        $included = [];

        $includedCallbacks = $this->getFilteredIncludes($relationships, $alreadyIncluded, $resourceData);
        foreach ($includedCallbacks as $relationship => $callback) {
            if (in_array($relationship, $relationships)) {
                $resource = $callback();

                if ($resource === null) {
                    continue;
                }

                $data = ($resource instanceof Collection)
                    ? $this->transformerFactory->createTransformerCollection($resource)->getData()
                    : [$this->transformerFactory->createTransformerItem($resource)->getData()];

                $included = array_merge($included, $data);
            }

            if (array_key_exists($relationship, $relationships)) {
                if (!isset($resource)) {
                    $resource = $callback();
                }

                $data = ($resource instanceof Collection)
                    ? $this->transformerFactory->createTransformerCollection($resource)
                    : $this->transformerFactory->createTransformerItem($resource);

                $included = array_merge($included, $data->getIncluded($relationships[$relationship]));
            }

            // Unset the resource so the isset check works correctly on the next iteration #phpvariablescoping
            unset($resource);
        }

        return $included;
    }

    /**
     * Filter which includes are still required to be included.
     *
     * @param array $keyFilter    keys we need to include
     * @param array $valueFilter  values already included
     * @param array $resourceData resource data containing relationships (to avoid expensive lookups)
     * @return array
     */
    protected function getFilteredIncludes(
        array $keyFilter = [],
        array $valueFilter = [],
        array $resourceData = [],
    ): array {
        $filtered = [];
        $valueFilter = array_map(function ($e) {
            return isset($e['type']) && isset($e['id']) ? $e['type'] . '-' . $e['id'] : '';
        }, $valueFilter);

        $included = $this->transformer->getIncluded($this->resource);

        foreach ($included as $key => $includes) {
            if (!in_array($key, $keyFilter) && !array_key_exists($key, $keyFilter)) {
                continue;
            }

            // Get the relationship data with key from the transformer.
            $relationships = array_key_exists('relationships', $resourceData)
                ? $resourceData['relationships']
                : $this->transformer->getRelationships($this->resource);
            $relationshipData = Arr::get($relationships, $key . '.data', []);

            // If relationship data is a single item instead of an array of items, we put it in an array.
            if (isset($relationshipData['type'])) {
                $relationshipData = [$relationshipData];
            }

            // We check for all the items if they are already included if not we add them to the list.
            foreach ($relationshipData as $relation) {
                if (!in_array($relation['type'] . '-' . $relation['id'], $valueFilter)) {
                    $filtered[$key] = $includes;
                    break;
                }
            }
        }

        return $filtered;
    }
}

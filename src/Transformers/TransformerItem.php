<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

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

            $relationships = $this->transformer->getRelationships($this->resource)[$key]['data'];

            if (isset($relationships['type'])) {
                $relationships = [$relationships];
            }

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
